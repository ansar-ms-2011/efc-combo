<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Models\ApplicationBiometric;
use App\Models\WorkflowTransition;
use Cache;
use App\Models\AdditionalCharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Exception;

class ApplicationControllerBackup extends Controller
{

    public function index(Request $request, $status = 'all')
    {
        $query = Application::with(['documents', 'biometrics', 'latestTransition']);

        if ($status !== 'all') {
            $query->where('current_status', $status);
        }

        if ($request->filled('filterBy') && $request->filled('search')) {

            $search = $request->search;

            switch ($request->filterBy) {

                case 'token':
                    $query->where('qmatic_token', 'like', "%$search%");
                    break;

                case 'cnic':
                    $query->where('cnic', 'like', "%$search%");
                    break;

                case 'name':
                    $query->where('first_name', 'like', "%$search%");
                    break;

                case 'missal':
                    $query->where('missalno', 'like', "%$search%");
                    break;
            }
        }
        if ($request->filled('region')) {
            $regionId = $request->region;

            $query->whereHas('district', function ($q) use ($regionId) {
                $q->where('parent_id', $regionId);
            });
        }

        if ($request->filled('service')) {
            $query->where('certificate_type', $request->service);
        }

        $applications = $query->orderBy('created_at', 'desc')->paginate(15);
        $additionalCharges = AdditionalCharge::where('primary_user_id', Auth::id())
            ->where('status', 'active')
            ->where('start_date', '<=', Carbon::now())
            ->where('end_date', '>=', Carbon::now())
            ->get();


        return response()->json([
            'success' => true,
            'data' => $applications,
            'additionalCharges' => $additionalCharges
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Basic validation
        $validator = Validator::make($request->all(), [
            'certificate_type' => 'required|in:domicile,state,both',
            'qmatic_token' => 'required|string|max:20',
            'cnic' => 'required|string|max:15',
            'dob' => 'required|date',
            'pob' => 'required|string|max:100',
            'father_name' => 'required|string|max:100',
            'phone' => 'required|string|max:15',
            'email' => 'required|email|max:100',
            'address' => 'required|string',
            'district' => 'required|exists:demographies,id',
            'city' => 'required|exists:demographies,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Map frontend fields to database fields
            $applicationData = [
                'certificate_type' => $request->certificate_type,
                'qmatic_token' => $request->qmatic_token,
                'application_type_id' => $request->application_type_id ?: null,
                'application_for_id' => $request->appointment_required_for ?: null,
                'missalno' => $request->missalno,

                // FIX: Convert '0' to null or find correct type ID
                'citizen_type_id' => $this->getCitizenTypeId($request->refugee_status),

                'refugee_from' => $request->refugee_from,
                'refugee_year' => $request->refugee_year,
                'first_name' => $request->first_name,
                'cnic' => $request->cnic,
                'dob' => $request->dob,
                'pob' => $request->pob,
                'identity_symbol' => $request->identity_symbol,
                'father_name' => $request->father_name,
                'father_cnic' => $request->father_cnic,

                // FIX: Convert to null if invalid
                'guardian_type_id' => $request->gaurdian ?: null,

                'authority_name' => $request->authority_name,
                'authority_designation' => $request->authority_designation,
                'email' => $request->email,
                'phone' => $request->phone,

                // FIX: Convert to null if invalid
                'religion_id' => $request->religion ?: null,
                'gender_id' => $request->gender ?: null,

                'occupation' => $request->occupation,
                'sakinah' => $request->sakinah,

                // FIX: Convert to null if invalid
                'marital_status_id' => $request->marital_status ?: null,
                'drjah' => $request->drjah,
                'wife_husband_name' => $request->wife_husband_name,
                'address' => $request->address,
                'address2' => $request->address2,
                'address3' => $request->address3,
                'address4' => $request->address4,

                // FIX: Convert to null if invalid
                'tehsil_id' => $request->city ?: null,
                'district_id' => $request->district ?: null,

                'location' => $request->location,
                'entry_date' => $request->entry_date,
                'entry_time' => $request->entry_time,
                'entry_month' => $request->entry_month,
                'remarks' => $request->remarks,
                'amount' => $request->amount ?? 0,
                'appointment_date' => $request->appointment_date,
                'appointment_time' => $request->appointment_time,
                'delivery_date' => $request->delivery_date,
                'center_id' => auth()->user()->center_id,
                'created_by' => auth()->id() ?? 1,
            ];

            // Clean empty values
            foreach ($applicationData as $key => $value) {
                if ($value === '' || $value === '0') {
                    $applicationData[$key] = null;
                }
            }

            // Generate unique token
            $applicationData['token'] = 'APP-' . strtoupper(uniqid());

            // Handle personal image
            if ($request->hasFile('personal_image')) {
                $personalImage = $request->file('personal_image');
                $imageName = 'applicant_' . time() . '.' . $personalImage->getClientOriginalExtension();
                $imagePath = $personalImage->storeAs('applications/personal_images', $imageName, 'public');
                $applicationData['personal_image'] = $imagePath;
            }

            Log::info('Application data to insert:', $applicationData);

            // Create the application
            $application = Application::create($applicationData);
            Log::info('Application created successfully', ['id' => $application->id]);

            // Handle documents
            if ($request->has('documents')) {
                $documents = $request->input('documents');
                Log::info('Processing documents:', ['count' => count($documents)]);

                foreach ($documents as $index => $docData) {
                    if ($request->hasFile("documents.{$index}.file")) {
                        $file = $request->file("documents.{$index}.file");
                        $fileName = 'document_' . time() . '_' . $index . '.' . $file->getClientOriginalExtension();
                        $filePath = $file->storeAs('applications/documents', $fileName, 'public');

                        ApplicationDocument::create([
                            'application_id' => $application->id,
                            'document_type' => $docData['type'] ?? 'other',
                            'certificate_category' => $docData['category'] ?? null,
                            'display_name' => $docData['displayName'] ?? $docData['type'],
                            'file_name' => $fileName,
                            'file_path' => $filePath,
                            'mime_type' => $file->getMimeType(),
                            'requires_photocopy' => ($docData['requirements']['photocopy'] ?? 'No') === 'Yes',
                            'requires_image' => ($docData['requirements']['image'] ?? 'No') === 'Yes',
                            'requires_original' => ($docData['requirements']['original'] ?? 'No') === 'Yes',
                            'is_verified' => $docData['verified'] ?? false,
                            'is_scanned' => $docData['scanned'] ?? false,
                            'created_by' => auth()->id() ?? 1,
                        ]);
                    }
                }
            }

            // Handle thumb impressions for state certificate
            if ($request->certificate_type === 'state' || $request->certificate_type === 'both') {
                $fingerTypes = ['thumb', 'index', 'middle', 'ring', 'little'];

                foreach ($fingerTypes as $fingerType) {
                    if ($request->hasFile("thumb_impressions.{$fingerType}")) {
                        $file = $request->file("thumb_impressions.{$fingerType}");
                        $fileName = "{$fingerType}_impression_" . time() . '.' . $file->getClientOriginalExtension();
                        $filePath = $file->storeAs('applications/biometrics', $fileName, 'public');

                        // Get feature_set data if provided
                        $featureSet = $request->input("thumb_features.{$fingerType}");

                        ApplicationBiometric::create([
                            'application_id' => $application->id,
                            'finger_type' => $fingerType,
                            'image_path' => $filePath,
                            'feature_set' => $featureSet,
                            'mime_type' => $file->getMimeType(),
                            'created_by' => auth()->id() ?? 1,
                        ]);
                    }
                }
            }

            // Create workflow transition
            WorkflowTransition::create([
                'application_id' => $application->id,
                'center_id' => auth()->user()->center_id,
                'from_user_id' => auth()->id(),
                'created_by' => auth()->id(),
            ]);

            // Generate and save QR code
            $this->saveQrCode($application);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Application submitted successfully',
                'data' => [
                    'application_id' => $application->id,
                    'token' => $application->token,
                    'certificate_type' => $application->certificate_type,
                ]
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to save application: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Failed to save application: ' . $e->getMessage(),
                'error' => config('app.debug') ? [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ] : null
            ], 500);
        }
    }

    /**
     * Get citizen type ID based on refugee_status
     */
    private function getCitizenTypeId($refugeeStatus)
    {
        if (empty($refugeeStatus) || $refugeeStatus === '0') {
            // Return ID for 'Local' from types table
            // You need to check what ID corresponds to 'Local' in your types table
            return 1; // Assuming 1 is the ID for 'Local'
        }

        if ($refugeeStatus === '1') {
            // Return ID for 'Refugee' from types table
            return 2; // Assuming 2 is the ID for 'Refugee'
        }

        return null;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $application = Application::with([
                'documents',
                'biometrics',
                'applicationType',
                'applicationFor',
                'citizenType',
                'guardianType',
                'religion',
                'gender',
                'maritalStatus',
                'tehsil',
                'district'
            ])->find($id);

            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }

            // Format the response to match frontend expectations
            $formattedData = [
                'id' => $application->id,
                'uuid' => $application->uuid,
                'first_name' => $application->first_name,
                'certificate_type' => $application->certificate_type,
                'qmatic_token' => $application->qmatic_token,
                'application_type_id' => $application->application_type_id,
                'appointment_required_for' => $application->application_for_id,
                'missalno' => $application->missalno,
                'refugee_status' => $application->citizen_type_id == 2 ? '1' : '0', // Assuming 2 is Refugee
                'refugee_from' => $application->refugee_from,
                'refugee_year' => $application->refugee_year,
                'authority_name' => $application->authority_name,
                'authority_designation' => $application->authority_designation,
                'dob' => $application->dob,
                'pob' => $application->pob,
                'cnic' => $application->cnic,
                'gaurdian' => $application->guardian_type_id,
                'father_cnic' => $application->father_cnic,
                'father_name' => $application->father_name,
                'identity_symbol' => $application->identity_symbol,
                'gender' => $application->gender_id,
                'religion' => $application->religion_id,
                'phone' => $application->phone,
                'email' => $application->email,
                'occupation' => $application->occupation,
                'sakinah' => $application->sakinah,
                'marital_status' => $application->marital_status_id,
                'drjah' => $application->drjah,
                'wife_husband_name' => $application->wife_husband_name,
                'remarks' => $application->remarks,
                'address' => $application->address,
                'address2' => $application->address2,
                'address3' => $application->address3,
                'address4' => $application->address4,
                'district' => $application->district_id,
                'city' => $application->tehsil_id,
                'tehsil_id' => $application->tehsil_id, // Add this for clarity
                'location' => $application->location,
                'entry_date' => $application->entry_date,
                'entry_time' => $application->entry_time,
                'entry_month' => $application->entry_month,
                'amount' => $application->amount,
                'appointment_date' => $application->appointment_date,
                'appointment_time' => $application->appointment_time,
                'delivery_date' => $application->delivery_date,
                'personal_image' => $application->personal_image ? Storage::url($application->personal_image) : null,
                'qr_code_data' => $application->uuid ? base64_encode(QrCode::format('svg')->size(200)->generate($application->uuid)) : null,

                // Format documents for frontend
                'documents' => $application->documents->map(function ($doc) {
                    return [
                        'id' => $doc->id,
                        'type' => $doc->document_type,
                        'category' => $doc->certificate_category,
                        'displayName' => $doc->display_name,
                        'fileName' => $doc->file_name,
                        'file_path' => $doc->file_path,
                        'fileURL' => $doc->file_path ? Storage::url($doc->file_path) : null,
                        'requirements' => [
                            'photocopy' => $doc->requires_photocopy ? 'Yes' : 'No',
                            'image' => $doc->requires_image ? 'Yes' : 'No',
                            'original' => $doc->requires_original ? 'Yes' : 'No'
                        ],
                        'verified' => (bool)$doc->is_verified,
                        'verified_by_ac' => (bool)$doc->verified_by_ac,
                        'verified_by_acr' => (bool)$doc->verified_by_acr,
                        'verified_by_dc' => (bool)$doc->verified_by_dc,
                        'scanned' => (bool)$doc->is_scanned
                    ];
                }),

                // Format biometrics for frontend
                'thumbImpressions' => [
                    'thumb' => $application->biometrics->where('finger_type', 'thumb')->first()?->image_path
                        ? Storage::url($application->biometrics->where('finger_type', 'thumb')->first()->image_path)
                        : null,
                    'index' => $application->biometrics->where('finger_type', 'index')->first()?->image_path
                        ? Storage::url($application->biometrics->where('finger_type', 'index')->first()->image_path)
                        : null,
                    'middle' => $application->biometrics->where('finger_type', 'middle')->first()?->image_path
                        ? Storage::url($application->biometrics->where('finger_type', 'middle')->first()->image_path)
                        : null,
                    'ring' => $application->biometrics->where('finger_type', 'ring')->first()?->image_path
                        ? Storage::url($application->biometrics->where('finger_type', 'ring')->first()->image_path)
                        : null,
                    'little' => $application->biometrics->where('finger_type', 'little')->first()?->image_path
                        ? Storage::url($application->biometrics->where('finger_type', 'little')->first()->image_path)
                        : null,
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $formattedData
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching application: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching application data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        Log::info('Application Update Request ID: ' . $id, $request->all());

        $application = Application::find($id);

        if (!$application) {
            return response()->json([
                'success' => false,
                'message' => 'Application not found'
            ], 404);
        }

        // Basic validation
        $validator = Validator::make($request->all(), [
            'qmatic_token' => 'required|string|max:20',
            'cnic' => 'required|string|max:15',
            'dob' => 'required|date',
            'pob' => 'required|string|max:100',
            'father_name' => 'required|string|max:100',
            'phone' => 'required|string|max:15',
            'email' => 'required|email|max:100',
            'address' => 'required|string',
            'district' => 'required|exists:demographies,id',
            'city' => 'required|exists:demographies,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Map frontend fields to database fields (same as store)
            $applicationData = [
                'certificate_type' => $request->certificate_type,
                'qmatic_token' => $request->qmatic_token,
                'application_type_id' => $request->application_type_id ?: null,
                'application_for_id' => $request->appointment_required_for ?: null,
                'missalno' => $request->missalno,
                'citizen_type_id' => $this->getCitizenTypeId($request->refugee_status),
                'refugee_from' => $request->refugee_from,
                'refugee_year' => $request->refugee_year,
                'first_name' => $request->first_name,
                'cnic' => $request->cnic,
                'dob' => $request->dob,
                'pob' => $request->pob,
                'identity_symbol' => $request->identity_symbol,
                'father_name' => $request->father_name,
                'father_cnic' => $request->father_cnic,
                'guardian_type_id' => $request->gaurdian ?: null,
                'authority_name' => $request->authority_name,
                'authority_designation' => $request->authority_designation,
                'email' => $request->email,
                'phone' => $request->phone,
                'religion_id' => $request->religion ?: null,
                'gender_id' => $request->gender ?: null,
                'occupation' => $request->occupation,
                'sakinah' => $request->sakinah,
                'marital_status_id' => $request->marital_status ?: null,
                'drjah' => $request->drjah,
                'wife_husband_name' => $request->wife_husband_name,
                'address' => $request->address,
                'address2' => $request->address2,
                'address3' => $request->address3,
                'address4' => $request->address4,
                'tehsil_id' => $request->city ?: null,
                'district_id' => $request->district ?: null,
                'location' => $request->location,
                'entry_date' => $request->entry_date,
                'entry_time' => $request->entry_time,
                'entry_month' => $request->entry_month,
                'remarks' => $request->remarks,
                'amount' => $request->amount ?? 0,
                'appointment_date' => $request->appointment_date,
                'appointment_time' => $request->appointment_time,
                'delivery_date' => $request->delivery_date,
                'updated_by' => auth()->id() ?? 1,
            ];

            // Clean empty values
            foreach ($applicationData as $key => $value) {
                if ($value === '' || $value === '0') {
                    $applicationData[$key] = null;
                }
            }

            // Handle personal image update
            if ($request->hasFile('personal_image')) {
                // Delete old image if exists
                if ($application->personal_image) {
                    Storage::disk('public')->delete($application->personal_image);
                }

                $personalImage = $request->file('personal_image');
                $imageName = 'applicant_' . time() . '.' . $personalImage->getClientOriginalExtension();
                $imagePath = $personalImage->storeAs('applications/personal_images', $imageName, 'public');
                $applicationData['personal_image'] = $imagePath;
            }

            // Update the application
            $application->update($applicationData);

            // Regenerate QR code if needed
            $this->saveQrCode($application);

            // Handle document updates
            if ($request->has('documents')) {
                $documents = $request->input('documents');
                Log::info('Processing documents for update:', ['count' => count($documents)]);

                foreach ($documents as $index => $docData) {
                    if ($request->hasFile("documents.{$index}.file")) {
                        $file = $request->file("documents.{$index}.file");
                        $fileName = 'document_' . time() . '_' . $index . '.' . $file->getClientOriginalExtension();
                        $filePath = $file->storeAs('applications/documents', $fileName, 'public');

                        ApplicationDocument::create([
                            'application_id' => $application->id,
                            'document_type' => $docData['type'] ?? 'other',
                            'certificate_category' => $docData['category'] ?? null,
                            'display_name' => $docData['displayName'] ?? $docData['type'],
                            'file_name' => $fileName,
                            'file_path' => $filePath,
                            'mime_type' => $file->getMimeType(),
                            'requires_photocopy' => ($docData['requirements']['photocopy'] ?? 'No') === 'Yes',
                            'requires_image' => ($docData['requirements']['image'] ?? 'No') === 'Yes',
                            'requires_original' => ($docData['requirements']['original'] ?? 'No') === 'Yes',
                            'is_verified' => $docData['verified'] ?? false,
                            'is_scanned' => $docData['scanned'] ?? false,
                            'created_by' => auth()->id() ?? 1,
                        ]);
                    }
                }
            }

            // Handle thumb impressions for state certificate
            if ($request->certificate_type === 'state' || $request->certificate_type === 'both') {
                $fingerTypes = ['thumb', 'index', 'middle', 'ring', 'little'];

                foreach ($fingerTypes as $fingerType) {
                    if ($request->hasFile("thumb_impressions.{$fingerType}")) {
                        // Find existing biometric
                        $existingBiometric = ApplicationBiometric::where('application_id', $application->id)
                            ->where('finger_type', $fingerType)
                            ->first();

                        // Delete physical file if exists
                        if ($existingBiometric && $existingBiometric->image_path) {
                            Storage::disk('public')->delete($existingBiometric->image_path);
                        }

                        $file = $request->file("thumb_impressions.{$fingerType}");
                        $fileName = "{$fingerType}_impression_" . time() . '.' . $file->getClientOriginalExtension();
                        $filePath = $file->storeAs('applications/biometrics', $fileName, 'public');

                        // Get feature_set data if provided
                        $featureSet = $request->input("thumb_features.{$fingerType}");

                        ApplicationBiometric::updateOrCreate(
                            [
                                'application_id' => $application->id,
                                'finger_type' => $fingerType,
                            ],
                            [
                                'image_path' => $filePath,
                                'feature_set' => $featureSet,
                                'mime_type' => $file->getMimeType(),
                                'updated_by' => auth()->id() ?? 1,
                            ]
                        );
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Application updated successfully',
                'data' => [
                    'application_id' => $application->id,
                    'token' => $application->token,
                    'certificate_type' => $application->certificate_type,
                ]
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to update application: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update application: ' . $e->getMessage(),
                'error' => config('app.debug') ? [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ] : null
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $application = Application::find($id);

        if (!$application) {
            return response()->json([
                'success' => false,
                'message' => 'Application not found'
            ], 404);
        }

        $application->delete();

        return response()->json([
            'success' => true,
            'message' => 'Application deleted successfully'
        ]);
    }

    // forward application
    public function forwardApplication(Request $request)
    {
        try {

            $application = Application::findOrFail($request->application_id);
            $currentStatus = $application->current_status;
            $action = $request->action ?? 'forward';

            if ($action === 'objected') {
                $fromStatus = $currentStatus;
                $toStatus = 'objected';
            } elseif ($action === 'rollback') {
                $fromStatus = $currentStatus;
                $lastTransition = WorkflowTransition::where('application_id', $application->id)->orderByDesc('id')->first();

                if (!$lastTransition) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Nothing to rollback'
                    ], 400);
                }

                $toStatus = $lastTransition->from_status;
            } else {
                if ($currentStatus === 'pending') {
                    $fromStatus = 'pending';
                    $toStatus = 'submitted';
                } elseif ($currentStatus === 'submitted') {
                    $fromStatus = 'submitted';
                    $toStatus = 'verified';
                } elseif ($currentStatus === 'verified') {
                    $fromStatus = 'verified';
                    $toStatus = 'approved';
                } elseif ($currentStatus === 'approved') {
                    $fromStatus = 'approved';
                    $toStatus = 'ready_for_delivery';
                } elseif ($currentStatus === 'ready_for_delivery') {
                    $fromStatus = 'ready_for_delivery';
                    $toStatus = 'delivered';
                } elseif ($currentStatus === 'objected') {
                    $fromStatus = 'objected';
                    $toStatus = 'pending';
                }
            }

            // update application status
            $application->update([
                'current_status' => $toStatus,
                'updated_at' => now(),
            ]);


            WorkflowTransition::create([
                'application_id' => $request->application_id,
                'from_status' => $fromStatus,
                'to_status' => $toStatus,
                'remarks' => $request->remarks,
                'from_user_id' => auth()->id(),
                'to_user_id' => $request->user_id,
                'action' => $action,
                'center_id' => auth()->user()->center_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Application updated successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update application: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getApplicationCurrentStatus($id)
    {
        try {
            $application = Application::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $application->current_status
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get application current status: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function applicationHistory($id)
    {
        try {

            $history = WorkflowTransition::where('application_id', $id)
                ->with([
                    'fromUser:id,name',
                    'toUser:id,name'
                ])
                ->orderByDesc('id')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'remarks' => $item->remarks,
                        'to_status' => $item->to_status,
                        'from_status' => $item->from_status,
                        'action' => $item->action,
                        'from_user' => $item->fromUser?->name,
                        'to_user' => $item->toUser?->name,
                        'created_at' => $item->created_at,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $history
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch history',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteFingerprint(string $id, string $fingerType)
    {
        try {
            $biometric = ApplicationBiometric::where('application_id', $id)
                ->where('finger_type', $fingerType)
                ->first();


            // public function forwardApplication(Request $request)
            // {
            //     try {
            //         $application = Application::findOrFail($request->application_id);
            //         $currentStatus = $application->current_status ?? 'pending';
            //         $action = $request->action ?? 'forward';

            if (!$biometric) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fingerprint not found'
                ], 404);
            }

            if ($biometric->image_path) {
                Storage::disk('public')->delete($biometric->image_path);
            }

            $biometric->delete();

            return response()->json([
                'success' => true,
                'message' => 'Fingerprint deleted successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete fingerprint: ' . $e->getMessage()
            ], 500);
        }
    }

    public function dashboardCounts()
    {
        $data = Cache::rememberForever('dashboard_counts', function () {
            return Application::select('current_status')
                ->selectRaw('COUNT(*) as total')
                ->groupBy('current_status')
                ->pluck('total', 'current_status');
        });

        return response()->json([
            'all' => array_sum($data->toArray()),
            'pending' => $data['pending'] ?? 0,
            'submitted' => $data['submitted'] ?? 0,
            'verified' => $data['verified'] ?? 0,
            'approved' => $data['approved'] ?? 0,
            'delivery' => $data['ready_for_delivery'] ?? 0,
            'delivered' => $data['delivered'] ?? 0,
            'objected' => $data['objected'] ?? 0,
        ]);
    }

    public function checkCnic(string $cnic)
    {
        try {
            $applications = Application::where('cnic', $cnic)->get();

            if ($applications->isEmpty()) {
                return response()->json([
                    'exists' => false,
                    'applications' => []
                ]);
            }

            $applicationsData = $applications->map(function ($app) {
                return [
                    'id' => $app->id,
                    'certificate_type' => $app->certificate_type
                ];
            });

            return response()->json([
                'exists' => true,
                'applications' => $applicationsData
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to check CNIC: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generateQrCode(string $id)
    {
        try {
            $application = Application::find($id);

            if (!$application || !$application->uuid) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found or UUID missing'
                ], 404);
            }

            $qrCode = QrCode::format('svg')
                ->size(200)
                ->generate($application->uuid);

            return response($qrCode)
                ->header('Content-Type', 'image/svg+xml');
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate QR code: ' . $e->getMessage()
            ], 500);
        }
    }

    private function saveQrCode($application)
    {
        try {
            if (!$application->uuid) {
                Log::warning('No UUID found for application: ' . $application->id);
                return;
            }

            $qrCode = base64_encode(QrCode::format('svg')->size(200)->generate($application->uuid));
            $qrImage = "data:image/svg+xml;base64,{$qrCode}";

            // Create PNG from SVG using GD
            $img = imagecreatetruecolor(200, 200);
            $white = imagecolorallocate($img, 255, 255, 255);
            imagefill($img, 0, 0, $white);

            // For now, save the SVG as base64 in database
            $fileName = 'qr_' . $application->id . '_' . time() . '.txt';
            $filePath = 'applications/qrcodes/' . $fileName;

            Storage::disk('public')->put($filePath, $application->uuid);

            $application->qr_code_path = $filePath;
            $application->saveQuietly();

            Log::info('QR code data saved', ['path' => $filePath]);
        } catch (Exception $e) {
            Log::error('Failed to save QR code: ' . $e->getMessage());
        }
    }

    public function viewDocument(string $id)
    {
        try {
            $documents = ApplicationDocument::where('application_id', $id)->get();

            $formattedDocuments = $documents->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'file_name' => $doc->file_name,
                    'file_path' => Storage::url($doc->file_path),
                    'document_type' => $doc->document_type,
                    'display_name' => $doc->display_name,
                    'is_verified' => (bool)$doc->is_verified,
                    'is_scanned' => (bool)$doc->is_scanned,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedDocuments,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch: ' . $e->getMessage()
            ], 500);
        }
    }

    public function verifyDocument(Request $request, string $id)
    {
        try {
            $document = ApplicationDocument::findOrFail($id);
            $user = auth()->user();
            $userRole = $user->roles->first()->name ?? null;

            // Update role-specific verification
            if ($userRole === 'AC') {
                $document->verified_by_ac = $request->is_verified;
            } elseif ($userRole === 'ACR') {
                $document->verified_by_acr = $request->is_verified;
            } elseif ($userRole === 'DC') {
                $document->verified_by_dc = $request->is_verified;
            }

            // Update general verification if all roles have verified
            $document->is_verified = $document->verified_by_ac && $document->verified_by_acr && $document->verified_by_dc;
            $document->save();

            return response()->json([
                'success' => true,
                'message' => 'Document verification status updated',
                'data' => [
                    'verified_by_ac' => (bool)$document->verified_by_ac,
                    'verified_by_acr' => (bool)$document->verified_by_acr,
                    'verified_by_dc' => (bool)$document->verified_by_dc,
                    'is_verified' => (bool)$document->is_verified,
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update verification status: ' . $e->getMessage()
            ], 500);
        }
    }
}
