<?php

namespace App\Services;

use App\Actions\Application\GenerateQr;
use App\Actions\Application\UploadBiometrics;
use App\Actions\Application\UploadDocuments;
use App\Actions\Application\UploadPersonalImage;
use App\Filters\ApplicationFilter;
use App\Jobs\DomicilePdfJob;
use App\Jobs\StateSubjectPdfJob;
use App\Mail\ApplicationStatusMail;
use App\Models\Applicant;
use App\Models\ApplicantChild;
use App\Models\Application;
use App\Models\Appointment;
use App\Models\CertificateJob;
use App\Models\DeliveryDetail;
use App\Models\Demography;
use App\Models\DuplicateDetail;
use App\Models\RefugeeDetail;
use App\Models\RequiredDocument;
use App\Models\WorkflowTransition;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Mail;
use Storage;

class ApplicationService
{
    public function __construct(
        private readonly UploadDocuments            $uploadDocuments,
        private readonly UploadBiometrics           $uploadBiometrics,
        private readonly GenerateQr                 $generateQr,
        private readonly ApplicationWorkflowService $workflow,
        private readonly UploadPersonalImage        $uploadPersonalImage,
    )
    {
    }

    public function index($request, $status)
    {
        $query = Application::with(['applicant' => function ($q) {
            $q->select('id', 'full_name', 'identity_number', 'identity_type');
        }, 'applicationType' => function ($q) {
            $q->select('id', 'name');
        }, 'appointment' => function ($q) {
            $q->select('id', 'qmatic_token', 'appointment_date', 'application_id');
        }]);

        if (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Commissioner')) {
            $query->when($request->district_id, function ($q) use ($request) {
                $q->where('district_id', $request->district_id);
            })->when($request->tehsil_id, function ($q) use ($request) {
                $q->where('tehsil_id', $request->tehsil_id);
            });

            $query->when($request->center_id, function ($q) use ($request) {
                $q->where('center_id', $request->center_id);
            });
        } else {
            if (auth()->user()->hasRole('DC')) {
                $query->where('district_id', auth()->user()->district_id);
            } else {
                $query->where('district_id', auth()->user()->district_id)
                    ->where('tehsil_id', auth()->user()->tehsil_id);
            }
        }

        if ($status !== 'all') {
            $query->where('current_status', $status);
        }

        if ($status === 'approved' || $status === 'delivered') {
            $query->with('certificates:id,application_id,type,pdf_path,preview_path');
        }

        ApplicationFilter::apply($query, $request);

        return $query->select([
            'id',
            'uuid',
            'tracking_token_no',
            'center_id',
            'current_status',
            'missal_no',
            'certificate_type',
            'application_type_id',
            'applicant_id',
            'created_at',
        ])
            ->orderByDesc('id')
            ->paginate(15);
    }

    public function store($data)
    {
        return DB::transaction(function () use ($data) {

            // Set applicant status
            $data['applicant']['status'] = 1;

            // Applicant - first or create
            $applicant = Applicant::firstOrCreate(
                [
                    'identity_number' => $data['applicant']['identity_number'],
                    'identity_type' => $data['applicant']['identity_type']
                ],
                $data['applicant']
            );

            // Refugee Details
            if (($data['applicant']['identity_type'] ?? null) === 'refugee'
                && !empty($data['applicant']['refugee_details'] ?? null)
            ) {
                RefugeeDetail::create(
                    array_merge(
                        $data['applicant']['refugee_details'],
                        ['applicant_id' => $applicant->id]
                    )
                );
            }

            // Prepare application data
            $applicationData = array_merge(
                $data['application'] ?? [],
                [
                    'applicant_id' => $applicant->id,
                    'current_status' => 'pending',
                    'amount' => 10,
                    'on_desk' => 'deo',
                    'center_id' => auth()->user()->center_id,
                    'created_by' => auth()->id() ?? 1,
                    'region_id' => Demography::find($data['application']['district_id'])?->parent_id,
                ]
            );

            // Application
            $application = Application::create($applicationData);

            // Duplicate Details if this is a duplicate application
            if ($application->application_type_id == 2) {
                DuplicateDetail::updateOrCreate([
                    'application_id' => $application->id,
                ], [
                    'application_id' => $application->id,
                    'reason_type_id' => $data['application']['duplicate_details']['reason_type_id'] ?? null,
                    'reason' => $data['application']['duplicate_details']['reason'] ?? null,
                ]);
            }

            $application->workFlows()->create([
                'from_status' => null,
                'to_status' => null,
                'action' => 'created',
                'remarks' => 'application created',
                'created_by' => auth()->id() ?? 1,
            ]);

            // Children
            if ($application->application_type_id == 1 && !empty($data['applicant']['children'] ?? null)) {
                foreach ($data['applicant']['children'] as $child) {
                    ApplicantChild::updateOrCreate([
                        'id'=>(int)$child['id'] ?? null,
                        'applicant_id' => $applicant->id,
                    ], [
                        ...$child,
                        'applicant_id' => $applicant->id,
                        'application_id' => $application->id
                    ]);
                }
            }

            // Prepare appointment data
            $appointmentData = array_merge(
                $data['application']['appointment'] ?? [],
                ['application_id' => $application->id]
            );

            // Appointment
            Appointment::create($appointmentData);

            // Prepare Delivery Details data
            $deliveryDetailData = array_merge(
                $data['application']['delivery_details'] ?? [],
                ['application_id' => $application->id]
            );

            // Create Delivery Details
            DeliveryDetail::create($deliveryDetailData);

            //This would be base path to store documents
            $appYearMonthDay = $application->created_at->format('Y/m/d');

            // Save Biometrics for state and both certificate types. Biometrics are nullable for duplicate applications.
            if (($data['application']['certificate_type'] === 'state' || $data['application']['certificate_type'] === 'both') && isset($data['application']['biometrics'])) {
                $this->uploadBiometrics->execute(
                    biometrics: $data['application']['biometrics'],
                    applicationId: $application->id,
                    applicantId: $applicant->id,
                    appYearMonthDay: $appYearMonthDay
                );
            }

            //Save Personal Image
            if ($data['application']['personal_image_file']) {
                $this->uploadPersonalImage->execute(
                    base64Image: $data['application']['personal_image_file'],
                    applicationId: $application->id,
                    appYearMonthDay: $appYearMonthDay
                );
            }

            // Save Required Documents
            $this->uploadDocuments->execute(
                base64Documents: $data['application']['documents'] ?? [],
                applicationId: $application->id,
                appYearMonthDay: $appYearMonthDay
            );

            // Save Application QR code
            $this->generateQr->execute(
                application: $application,
                appYearMonthDay: $appYearMonthDay
            );

            return $application;
        });
    }

    public function update($data, Application $application)
    {
        return DB::transaction(function () use ($data, $application) {

            // Application
            $application->update($data['application']);
            $application->workFlows()->create([
                'from_status' => null,
                'to_status' => null,
                'action' => 'created',
                'remarks' => 'application updated',
                'created_by' => auth()->id() ?? 1,
            ]);
            $application->refresh();
            $application->load('applicant');

            // Applicant
            $application->applicant->update($data['applicant']);

            // Children
            $incomingChildren = $data['applicant']['children'] ?? [];

            // Get incoming IDs (existing children)
            $incomingIds = collect($incomingChildren)->pluck('id')->filter()->values()->toArray();

            // Delete children not in the incoming list
            $application->applicant->children()->whereNotIn('id', $incomingIds)->delete();

            foreach ($incomingChildren as $child) {
                $childData = Arr::only($child, (new ApplicantChild)->getFillable());

                if (!empty($child['id'])) {
                    ApplicantChild::where('id', $child['id'])
                        ->where('applicant_id', $application->applicant->id)
                        ->update($childData);
                } else {
                    ApplicantChild::create([
                        ...$childData,
                        'applicant_id' => $application->applicant->id,
                        'application_id' => $application->id
                    ]);
                }
            }

            // Refugee Details
            if ($data['applicant']['identity_type'] === 'refugee') {
                RefugeeDetail::updateOrCreate(
                    ['applicant_id' => $application->applicant->id],
                    [
                        ...$data['applicant']['refugee_details'],
                        'applicant_id' => $application->applicant->id,
                    ]
                );
            } else {
                RefugeeDetail::where('applicant_id', $application->applicant->id)->delete();
            }


            // Update or Create Appointment
            Appointment::updateOrCreate(
                ['application_id' => $application->id],
                [
                    ...$data['application']['appointment'],
                    'application_id' => $application->id,
                ]
            );

            // Duplicate Details if this is a duplicate application
            if ($application->application_type_id == 2) {
                DuplicateDetail::updateOrCreate([
                    'application_id' => $application->id,
                ], [
                    'application_id' => $application->id,
                    'reason_type_id' => $data['application']['duplicate_details']['reason_type_id'] ?? null,
                    'reason' => $data['application']['duplicate_details']['reason'] ?? null,
                ]);
            } else {
                DuplicateDetail::where('application_id', $application->id)->delete();
            }

            // Update or Create Delivery Details
            DeliveryDetail::updateOrCreate(
                ['application_id' => $application->id],
                [
                    ...$data['application']['delivery_details'],
                    'application_id' => $application->id,
                ]
            );

            //This would be base path to store documents
            $appYearMonthDay = $application->created_at->format('Y/m/d');

            // Save Biometrics for state and both certificate types
            if (($data['application']['certificate_type'] === 'state' || $data['application']['certificate_type'] === 'both') && isset($data['application']['biometrics'])) {
                $this->uploadBiometrics->execute(
                    biometrics: $data['application']['biometrics'],
                    applicationId: $application->id,
                    applicantId: $application->applicant_id,
                    appYearMonthDay: $appYearMonthDay
                );
            }

            //Save Personal Image
            if (isset($data['application']['personal_image_file'])) {
                $this->uploadPersonalImage->execute(
                    base64Image: $data['application']['personal_image_file'],
                    applicationId: $application->id,
                    appYearMonthDay: $appYearMonthDay
                );
            }

            // Save Required Documents
            $this->uploadDocuments->execute(
                base64Documents: $data['application']['documents'] ?? [],
                applicationId: $application->id,
                appYearMonthDay: $appYearMonthDay
            );

            // QR
            $this->generateQr->execute(
                application: $application,
                appYearMonthDay: $appYearMonthDay
            );

            return $application;
        });
    }

    public function show($uuid): array
    {
        $application = Application::with([
            'applicant' => function ($q) {
                $q->with(['children', 'refugeeDetails']);
            },
            'appointment',
            'documents',
            'biometrics',
            'applicationType',
            'approvals',
            'duplicateDetails',
            'deliveryDetails',
        ])->where('uuid', $uuid)->firstOrFail();

        $reqDocs = RequiredDocument::active()->get();
        $list = $reqDocs->map(function ($doc) use ($application) {
            $appDoc = $application->documents->where('required_document_id', $doc->id)->first();
            if ($appDoc) {
                return [
                    ...$doc->toArray(),
                    ...$appDoc->toArray(),
                    'app_doc_id' => $appDoc->id,
                    'new_file' => null,
                    'upload_method' => null,
                ];
            } else {
                return [
                    ...$doc->toArray(),
                    'application_id' => null,
                    'required_document_id' => $doc->id,
                    'upload_method' => null,
                    'new_file' => null,
                    'file_path' => null,
                    'mime_type' => null,
                    'original_name' => null,
                    'ac_acr_verified' => null,
                    'ac_acr_verified_date' => null,
                    'dc_verified' => null,
                    'dc_verified_date' => null,
                    'app_doc_id' => null,
                ];
            }
        });

        // ✅ Extract applicant BEFORE toArray
        $applicant = $application->applicant;

        // ✅ Convert to array
        $applicationArray = $application->toArray();
        $applicationArray['documents'] = $list->toArray();

        // ✅ Transform biometrics on ARRAY (not model)
        $applicationArray['biometrics'] = collect($applicationArray['biometrics'])
            ->mapWithKeys(function ($biometric) {
                return [
                    $biometric['finger_type'] => [
                        ...$biometric,
                    ]
                ];
            })
            ->toArray();

        // ✅ Remove the applicant from the application
        unset($applicationArray['applicant']);

        return [
            'application' => $applicationArray,
            'applicant' => $applicant,
            'list' => $list,
        ];
    }

    public function forward($data)
    {
        try {
            DB::beginTransaction();

            $app = Application::where('uuid', $data['app_uuid'])->firstOrFail();


            $user = auth()->user();

            // AC / ACR / DC ke liye validation
            if ($user->hasRole('AC') || $user->hasRole('ACR') || $user->hasRole('DC')) {

                // e_sign check
                if (empty($user->sign_file)) {
                    throw ValidationException::withMessages([
                        'esign' => 'Your e-Signature are missing in your profile. Update your signature in profile setting page or contact system Admin to update your profile.'
                    ]);
                }


                // storage me file check
                if (!Storage::disk('public')->exists($user->sign_file)) {
                    throw ValidationException::withMessages([
                        'esign' => 'Invalid e-Signature file. Contact Super Admin to update your profile.'
                    ]);
                }
            }

            // verify documents
            if ($data['action'] === 'forward') {
                if (auth()->user()->hasRole('AC') || auth()->user()->hasRole('ACR')) {
                    $app->documents()->update(['ac_acr_verified' => 1, 'ac_acr_verified_date' => now()]);
                } else if (auth()->user()->hasRole('DC')) {
                    $app->documents()->update(['dc_verified' => 1, 'dc_verified_date' => now()]);
                }
            }

            $currentStatus = $app->current_status;
            $newStatus = $this->workflow->next($app, $data['action']);

            $approvalDetail = ['current_status' => $newStatus];

            $user = auth()->user();

            if (($newStatus === 'verified' || $newStatus === 'approved') || ($user->hasRole('AC') || $user->hasRole('ACR') || $user->hasRole('DC'))) {
                $approvalDetail['application_id'] = $app->id;
                $approvalDetail['officer_id'] = $user->id;
                $approvalDetail['officer_name'] = $user->name;
                $approvalDetail['designation'] = $user->employee?->designation?->name ?? $user->designation;
                $approvalDetail['sign_date'] = now();
                $approvalDetail['level'] = $user->roles[0]->name;

                if ($user->sign_file) {
                    $approvalDetail['esign'] = $user->e_sign ?: $user->sign_file;
                }
                // Save approval detail
                $app->approvals()->create($approvalDetail);

                //Queue PDF generation Job when the application is approved
                if ($user->hasRole('DC') && $newStatus == 'approved') {
                    DB::afterCommit(function () use ($app) {
                        $this->queueCertificateJobs($app);
                    });
                }

                // Save processing days when DC approves the application
                if ($user->hasRole('DC') && $newStatus == 'approved') {
                    $processingDays = now()->diffInDays($app->created_at);
                    $app->processing_days = abs($processingDays);
                }
            }

            //When application is finally approved then application goes to archived status
            $app->update([
                'current_status' => $newStatus,
                'on_desk' => match ($newStatus) {
                    'submitted' => 'AC',
                    'verified' => 'DC',
                    default => 'deo',
                },
                'lifecycle_status' => $newStatus === 'approved' ? 'archived' : 'active',
            ]);

            WorkflowTransition::create([
                'application_id' => $app->id,
                'from_status' => $currentStatus,
                'to_status' => $newStatus,
                'remarks' => $data['remarks'],
                'action' => $data['action'],
                'created_by' => $user->id,
                'created_at' => now()
            ]);

            // ✅ IMPORTANT: EMAIL REGISTER INSIDE TRANSACTION
            DB::afterCommit(function () use ($app, $newStatus, $user) {

                $email = $app->applicant->email ?? null;
                if (!$email) return;

                $statusMap = [
                    'pending' => 'Pending',
                    'submitted' => 'Forwarded to AC',
                    'verified' => 'Forwarded to DC',
                    'approved' => 'Approved',
                    'delivered' => 'Delivered',

                ];

                Mail::to($email)->queue(
                    new ApplicationStatusMail(
                        $app->load('applicant'),
                        $user->roles[0]->name ?? 'Officer',
                        $statusMap[$newStatus] ?? $newStatus
                    )
                );
            });

            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

    }

    private function queueCertificateJobs($application): void
    {
        if (in_array($application->certificate_type, ['state', 'both'])) {
            $certificateJob = CertificateJob::create([
                'application_id' => $application->id,
                'type' => 'state',
                'status' => 'pending',
                'started_at' => now(),
                'completed_at' => null,
                'message' => null
            ]);
            StateSubjectPdfJob::dispatch($application->id, $certificateJob->id);
        }
        if (in_array($application->certificate_type, ['domicile', 'both'])) {
            $certificateJob = CertificateJob::create([
                'application_id' => $application->id,
                'type' => 'domicile',
                'status' => 'pending',
                'started_at' => now(),
                'completed_at' => null,
                'message' => null
            ]);
            DomicilePdfJob::dispatch($application->id, $certificateJob->id);
        }
    }

    public function getWorkFlowHistory(string $id)
    {
        return WorkflowTransition::with('createdBy:id,first_name,last_name,email')
            ->where('application_id', $id)
            ->orderByDesc('id')
            ->get();
    }
}
