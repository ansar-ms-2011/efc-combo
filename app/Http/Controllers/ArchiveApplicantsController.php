<?php

namespace App\Http\Controllers;

use App\Actions\ProcessZipAction;
use App\Http\Requests\ArchivedZipRequest;
use App\Http\Requests\ArchiveApplicantRequest;
use App\Jobs\ProcessArchivedZipJob;
use App\Models\{Applicant, RefugeeDetail, ApplicationCertificate};
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\{Request};
use Illuminate\Support\Facades\{Storage, DB, Log, File};
use Illuminate\Support\Str;

class ArchiveApplicantsController extends Controller
{
    //  Display a listing of archived applications.

    public function index(Request $request)
    {
        $query = ApplicationCertificate::with([
            'applicant.refugeeDetails',
            'uploader',
            'verification.dataEnterer',
            'verification.imageUploader',
            'applicant.tehsil'
        ]);

        // User Filtering
        if ($request->filled('user_id')) {
            $userId = $request->user_id;
            $query->where(function ($q) use ($userId, $request) {
                if ($request->data_entry == 'completed') {
                    $q->whereHas('verification', fn($v) => $v->where('data_enter_by', $userId));
                } elseif ($request->data_entry == 'all_scanned') {
                    $q->whereHas('verification', fn($v) => $v->where('img_upload_by', $userId));
                } elseif ($request->verification_status == 'verified') {
                    $q->where('uploaded_by', $userId)
                        ->whereHas('verification', fn($v) => $v->where('status', 'verified'));
                } else {
                    $q->where('uploaded_by', $userId);
                }
            });
        }

        if ($request->filled('data_entry')) {
            if ($request->data_entry == 'completed') {
                $query->whereHas('applicant', fn($q) => $q->where('identity_number', '!=', 'DRAFT-00000'));
            } elseif ($request->data_entry == 'all_scanned') {
                $query->whereHas('verification', fn($q) => $q->whereNotNull('img_upload_by'));
            }
        }
        //  Search & Other Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('applicant', function ($q) use ($search) {
                $q->where('full_name', 'like', "%$search%")
                    ->orWhere('identity_number', 'like', "%$search%");
            })->orWhere('misal_no', 'like', "%$search%");
        }
        if ($request->filter == 'today') {
            $query->whereDate('created_at', now()->today());
        }
        if ($request->filled('verification_status')) {
            $query->whereHas('verification', fn($q) => $q->where('status', $request->verification_status));
        }
        return response()->json([
            'message' => 'Certificates fetched successfully',
            'data' => $query->latest()->paginate($request->query('per_page', 10))
        ]);
    }

    //  Store a new archived application.

    public function store(ArchiveApplicantRequest $request, ArchivedZipRequest $zipRequest)
    {
        if ($request->hasFile('zip_file')) {
            return $this->processZipBatch($zipRequest);
        }
        return $this->processApplication($request);
    }


    //   Display application.

    public function show($id)
    {
        $certificate = ApplicationCertificate::with(['applicant.refugeeDetails', 'uploader'])->findOrFail($id);
        return response()->json(['success' => true, 'data' => $certificate]);
    }

    //   Update the specified application.
    public function update(ArchiveApplicantRequest $request, $id)
    {
        return $this->processApplication($request, $id);
    }

    //  Store and Update 
    private function processApplication(ArchiveApplicantRequest $request, $id = null)
    {
        DB::beginTransaction();
        try {
            $isUpdate = (bool)$id;
            $certificate = $isUpdate ? ApplicationCertificate::findOrFail($id) : new ApplicationCertificate();
            $applicantId = null;
            $isDataEntered = $request->filled('cnic') || $request->filled('refugee_number');
            $hasNewImage = !empty($doc['file_path']) && str_starts_with($doc['file_path'], 'data:');

            //  Handle Applicant
            $idNumber = $request->is_refugee ? $request->refugee_number : $request->cnic;
            if ($request->filled('cnic') || $request->filled('refugee_number')) {
                $applicant = Applicant::updateOrCreate(
                    ['identity_number' => $idNumber],
                    [
                        'full_name' => $request->name,
                        'father_name' => $request->father_name,
                        'identity_type' => $request->is_refugee ? 'refugee' : 'local',
                        'tehsil_id' => $request->tehsil_id,
                        'status' => 1,
                        'created_by' => auth()->id() ?? 1,
                    ]
                );
                $applicantId = $applicant->id;
            } else {

                $draftApplicant = Applicant::firstOrCreate(
                    ['identity_number' => 'DRAFT-00000'],
                    [
                        'full_name'         => 'Draft  Applicant',
                        'father_name'     => 'N/A',
                        'identity_type'     => 'local',
                        'status'            => 1,
                        'created_by'        => auth()->id() ?? 1,
                    ]
                );
                $applicantId = $draftApplicant->id;
            }

            //  Handle Documents 
            if ($request->has('documents') && count($request->documents) > 0) {
                foreach ($request->documents as $doc) {
                    $dbPath = $this->uploadBase64File($doc['file_path'], $certificate->pdf_path);
                    if (!$isUpdate) {
                        // Create Certificate
                        $newCert = ApplicationCertificate::create([
                            'applicant_id' => $applicantId,
                            'type' => $request->identity_type,
                            'source' => 'archive',
                            'certificate_number' => 'CERT-' . strtoupper(Str::random(10)),
                            'issue_date' => now(),
                            'pdf_path' => $dbPath,
                            'misal_no' => $request->misal_no,
                            'uploaded_by' => auth()->id() ?? 1,
                        ]);
                        DB::table('application_verifications')->insert([
                            'application_certificate_id' => $newCert->id,
                            'status' => 'pending',
                            'img_upload_by' => auth()->id(),
                            'data_enter_by' => $isDataEntered ? auth()->id() : null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    } else {
                        // Update Certificate
                        $certificate->update([
                            'applicant_id' => $applicantId,
                            'pdf_path' => $dbPath,
                            'type' => $request->identity_type,
                            'misal_no' => $request->misal_no,
                            'issue_date' =>  $request->issue_date,

                        ]);

                        if ($hasNewImage) {
                            $dbPath = $this->uploadBase64File($doc['file_path'], $certificate->pdf_path);
                            $certificate->update(['pdf_path' => $dbPath]);

                            $certificate->verification()->update([
                                'status'        => 'pending',
                                'img_upload_by' => auth()->id(), // ✅ sirf image wale ki id
                                'updated_at'    => now(),
                            ]);
                        }
                        if ($isDataEntered) {
                            $certificate->verification()->update([
                                'status'        => 'pending',
                                'data_enter_by' => auth()->id(), // ✅ sirf data enter wale ki id
                                'updated_at'    => now(),
                            ]);
                        }
                    }
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => $isUpdate ? 'Application updated successfully!' : 'Application saved successfully!'
            ], $isUpdate ? 200 : 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Archive Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()], 500);
        }
    }

    //   delete application and linked records.
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $certificate = ApplicationCertificate::findOrFail($id);
            $certificate->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Deleted successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    //   Verification 
    public function verifyApplication(Request $request)
    {
        $request->validate([
            'certificate_id' => 'required|exists:application_certificates,id',
            'status' => 'required|in:verified,rejected',
            'remarks' => 'nullable|string|max:255',
        ]);

        try {
            $updated = DB::table('application_verifications')
                ->where('application_certificate_id', $request->certificate_id)
                ->update([
                    'status'      => $request->status,
                    'verified_by' => auth()->id(),
                    'verified_at' => now(),
                    'remarks'     => $request->remarks ?? 'Verified through Archive Portal',
                    'updated_at'  => now(),
                ]);

            if (!$updated) {
                DB::table('application_verifications')->insert([
                    'application_certificate_id' => $request->certificate_id,
                    'status'      => $request->status,
                    'verified_by' => auth()->id(),
                    'verified_at' => now(),
                    'remarks'     => $request->remarks ?? 'Verified through Archive Portal',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }

            $msg = $request->status === 'verified' ? 'Verified successfully!' : 'Application Rejected!';
            return response()->json(['success' => true, 'message' => $msg]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    /**
     * Base64 and Upload
     */
    private function uploadBase64File($base64Data, $oldPath = null)
    {
        if (preg_match('/^data:(\w+\/\w+);base64,/', $base64Data, $type)) {
            $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
            $extension = strtolower(explode('/', $type[1])[1]);

            // delete old file 
            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }

            $fileName = 'archived_' . time() . '_' . Str::random(5) . '.' . $extension;
            $dbPath = 'archived_certificates/' . $fileName;
            Storage::disk('public')->put($dbPath, base64_decode($base64Data));

            return $dbPath;
        }
        return $oldPath;
    }

    public function getScannersList()
    {
        $users = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['Scanner', 'super-admin', 'Supervisor']);
        })->select('id', 'first_name', 'last_name')->get();

        return response()->json(['data' => $users]);
    }

    public function getArchiveDashboard(Request $request)
    {
        $targetUserId = $request->user_id;
        $fromDate = $request->from_date;
        $toDate = $request->to_date;

        $query = ApplicationCertificate::query();
        if ($targetUserId) {
            $query->where('uploaded_by', $targetUserId);
        }
        if ($fromDate && $toDate) {
            $query->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
        }

        $counts = [
            'total' => (clone $query)->when($fromDate && $toDate, function ($q) use ($fromDate, $toDate) {
                return $q->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
            })->count(),
            'today' => (clone $query)->whereDate('created_at', Carbon::today())->count(),
            'week'  => (clone $query)->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(),

            'total_scanned' => DB::table('application_verifications')
                ->when($targetUserId, fn($q) => $q->where('img_upload_by', $targetUserId))
                ->when($fromDate && $toDate, fn($q) => $q->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']))
                ->count(),

            'total_data_entered' => DB::table('application_verifications')
                ->when($targetUserId, fn($q) => $q->where('data_enter_by', $targetUserId))
                ->whereNotNull('data_enter_by')
                ->when($fromDate && $toDate, fn($q) => $q->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']))
                ->count(),
            'total_verified'  => DB::table('application_verifications')
                ->join('application_certificates', 'application_verifications.application_certificate_id', '=', 'application_certificates.id')
                ->where('application_verifications.status', 'verified')
                ->when($targetUserId, function ($q) use ($targetUserId) {
                    return $q->where('application_certificates.uploaded_by', $targetUserId);
                })
                ->when($fromDate && $toDate, function ($q) use ($fromDate, $toDate) {
                    return $q->whereBetween('application_verifications.verified_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
                })
                ->count(),
        ];

        $operatorsQuery = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['Scanner', 'super-admin', 'Supervisor']);
        });

        if ($targetUserId) {
            $operatorsQuery->where('id', $targetUserId);
        }

        $topOperators = $operatorsQuery->get()->map(function ($user) use ($fromDate, $toDate) {
            $q = ApplicationCertificate::where('uploaded_by', $user->id);

            if ($fromDate && $toDate) {
                $q->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
            }

            return [
                'id' => $user->id,
                'name' => $user->first_name . ' ' . ($user->last_name ?? ''),
                'today_count' => ApplicationCertificate::where('uploaded_by', $user->id)->whereDate('created_at', Carbon::today())->count(),
                'total_count' => $q->count(),
            ];
        })
            ->filter(fn($op) => $op['total_count'] > 0)
            ->sortByDesc('total_count')->values()->all();

        return response()->json([
            'counts' => $counts,
            'top_operators' => $topOperators
        ]);
    }

    public function getArchiveReport(Request $request)
    {
        // Applicant -> Tehsil -> District -> Region
        $query = ApplicationCertificate::with([
            'applicant.tehsil.parent.parent',
            'uploader',
            'verification.dataEnterer',
            'verification.imageUploader',

        ]);
        // Scanner Filter
        if ($request->filled('user_id')) {
            $query->where('uploaded_by', $request->user_id);
        }

        // Date Range Filter
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('created_at', [$request->from_date . ' 00:00:00', $request->to_date . ' 23:59:59']);
        }

        $query->whereHas('applicant', function ($q) use ($request) {
            $q->where(function ($sub) use ($request) {
                if ($request->filled('tehsil_ids') && $request->tehsil_ids !== '') {
                    $sub->orWhereIn('tehsil_id', explode(',', $request->tehsil_ids));
                }
                // District Filter
                if ($request->filled('district_ids') && $request->district_ids !== '') {
                    $sub->orWhereHas('tehsil', function ($sq) use ($request) {
                        $sq->whereIn('parent_id', explode(',', $request->district_ids));
                    });
                }
                // Region Filter
                if ($request->filled('region_ids') && $request->region_ids !== '') {
                    $sub->orWhereHas('tehsil.parent', function ($sq) use ($request) {
                        $sq->whereIn('parent_id', explode(',', $request->region_ids));
                    });
                }
            });
        });

        $data = $query->latest()->get();

        return response()->json([
            'data' => $data,
            'total_count' => $data->count()
        ]);
    }

    private function processZipBatch(ArchivedZipRequest $request)
    {
        $zipFile = $request->file('zip_file');
        $userId = auth()->id() ?? 1;
        $limit = 1 * 1024 * 1024; // 1MB
        try {
            if ($zipFile->getSize() > $limit) {
                $fileName = uniqid() . '.zip';
                $tempDir = storage_path('app/ArchiveImages');
                if (!File::exists($tempDir)) {
                    File::makeDirectory($tempDir, 0755, true);
                }
                $fullPath = $tempDir . DIRECTORY_SEPARATOR . $fileName;
                $zipFile->move($tempDir, $fileName);
                if (!file_exists($fullPath)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'File save failed: ' . $fullPath
                    ], 500);
                }

                Log::info("ZIP saved at: " . $fullPath . " | Size: " . filesize($fullPath));
                ProcessArchivedZipJob::dispatch($fullPath, $userId);
                return response()->json([
                    'success' => true,
                    'message' => 'Large ZIP detected. Processing in background.'
                ]);
            } else {
                $action = new ProcessZipAction();
                $count = $action->execute($zipFile->getRealPath(), $userId);
                return response()->json([
                    'success' => true,
                    'message' => $count . ' files processed successfully!'
                ]);
            }
        } catch (\Exception $e) {
            Log::error("processZipBatch Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    
}
