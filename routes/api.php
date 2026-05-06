<?php

use App\Http\Controllers\AdditionalChargeController;
use App\Http\Controllers\ApplicantController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\ArchiveApplicantsController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\CenterController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\CertificateJobsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DemographyController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\KpiController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\RequiredDocumentController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ServiceCenterController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ServiceInstructionController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\TransferDetailController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/api-test', function () {
    return response()->json(['message' => 'API is working']);
});

/*
|--------------------------------------------------------------------------
| Protected Routes (Sanctum)
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth:sanctum',
    'logRequestTime',
    'trackUserActivity',
    'apiCallLog',
])->group(function () {


    Route::get('user', function (Request $request) {
        $user = Auth::user();
        $user->role = $user->getRoleNames()->first();
        $user->permissions = $user->getPermissionsViaRoles()->pluck('name');
        $allowedServices = $user->serviceCenters
            ->pluck('service.name')
            ->map(fn($name) => strtolower(trim($name)))
            ->unique()
            ->values();

        $user->allowed_services = $allowedServices;

        return $user;

    });



    // Resources
    Route::apiResource('departments', DepartmentController::class);
    Route::apiResource('centers', CenterController::class);

    Route::apiResource('users', UserController::class);
    Route::get('get-users-dropdown-data', [UserController::class, 'getUserDropdownData']);

    Route::apiResource('services', ServiceController::class);
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('types', TypeController::class);
    Route::apiResource('additional-charges', AdditionalChargeController::class);
    Route::apiResource('media', MediaController::class);
    Route::apiResource('templates', TemplateController::class);

    Route::get('applicant-details', [ApplicantController::class, 'getApplicantDetails']);
    Route::get('applications/check-cnic/{cnic}', [ApplicationController::class, 'checkCnic']);
    Route::get('applications/list/{status?}', [ApplicationController::class, 'index']);
    Route::get('applications/{id}/qrcode', [ApplicationController::class, 'generateQrCode']);
    Route::apiResource('applications', ApplicationController::class)->except(['index']);
    Route::get('applications/{id}/workflow-history', [ApplicationController::class, 'workflowHistory']);
    Route::get('applications/current-status/{id}', [ApplicationController::class, 'getApplicationCurrentStatus']);
    Route::apiResource('demographies', DemographyController::class);
    Route::apiResource('serviceinstructions', ServiceInstructionController::class);
    Route::get('/application-history/{id}', [ApplicationController::class, 'applicationHistory']);
    // Route::get('/dashboard-counts', [DashboardController::class, 'dashboardCounts']);
    Route::get('/dashboard-counts', [DashboardController::class, 'index']);
    Route::get('/get-charges', [AdditionalChargeController::class, 'getCharges']);
    Route::post('/service-centers', [ServiceCenterController::class, 'store']);
    Route::get('/service-centers', [ServiceCenterController::class, 'index']);
    // Route::post('/user-service-assign', [UserController::class, 'assignServiceToUser']);
    Route::delete('/service-centers/{center}', [ServiceCenterController::class, 'destroy']);
    Route::put('/service-centers', [ServiceCenterController::class, 'update']);
    Route::get('/service-centers/{center}', [ServiceCenterController::class, 'show']);
    Route::get('/employee/profile', [EmployeeController::class, 'index']);
    Route::put('/employee/profile', [EmployeeController::class, 'update']);
    Route::put('/employee/change-password', [EmployeeController::class, 'changePassword']);
    Route::get('/user/allowed-services', [UserController::class, 'getAllowedServices']);

    //  Route::apiResource('web-settings', WebSettingController::class);


    // Archive Applicants
Route::apiResource('archive-applicants', ArchiveApplicantsController::class);
Route::post('/verify-application', [ArchiveApplicantsController::class, 'verifyApplication']);
Route::get('/scanners-list', [ArchiveApplicantsController::class, 'getScannersList']);
Route::get('/archive-dashboard', [ArchiveApplicantsController::class, 'getArchiveDashboard']);
Route::get('/archive-report', [ArchiveApplicantsController::class, 'getArchiveReport']);
Route::post('/archive-bulk-upload', [ArchiveApplicantsController::class, 'bulkUploadZip']);






    Route::apiResource('required-documents', RequiredDocumentController::class)
        ->only(['index', 'store', 'destroy', 'update', 'show']);
     
    Route::post('/user-transfer', [TransferDetailController::class, 'store']);    


    // Custom Application Route
    Route::post('/applications/store', [ApplicationController::class, 'store']);
    Route::delete('/applications/{id}/fingerprint/{fingerType}', [ApplicationController::class, 'deleteFingerprint']);
    Route::get('/applications/{id}/documents', [ApplicationController::class, 'viewDocument']);
    Route::post('/applications/documents/{id}/verify', [ApplicationController::class, 'verifyDocument']);

    // Demography
    Route::get('/demographies/parents/{type}', [DemographyController::class, 'parents']);
    Route::get('/districts', [DemographyController::class, 'districts']);
    Route::get('/tehsils-list', [DemographyController::class, 'tehsils']);

    // Types
    Route::get('/working-days', [TypeController::class, 'workingDays']);
    Route::get('/types/parent/{name}', [TypeController::class, 'getTypesByParentName']);
    Route::get('/get-grouped-types', [TypeController::class, 'getGroupedTypes']);

    // Roles & Centers helpers
    Route::get('/modules', [RoleController::class, 'getModules']);
    Route::get('/get-roles', [RoleController::class, 'getRoles']);
    Route::get('/get-centers', [CenterController::class, 'getCenters']);
    Route::get('/get-tehsils', [DemographyController::class, 'getTehsils']);
    Route::get('/get-users', [UserController::class, 'getUsers']);
    Route::post('forward-application', [ApplicationController::class, 'forwardApplication']);

    Route::get('ping', fn() => response()->json(['ok' => true]));

    // Backups
    Route::get('/backups', [BackupController::class, 'index']);
    Route::post('/backups', [BackupController::class, 'store']);
    Route::get('/backups/{backup}', [BackupController::class, 'show']);
    Route::get('/backups/{backup}/download', [BackupController::class, 'download']);

    
    // CertificateJobs Routes
    Route::get('/certificate-jobs', [CertificateJobsController::class, 'index']);
    Route::post('/certificate-jobs/{jobId}/re-initiate', [CertificateJobsController::class, 'reInitiateJob']);

    Route::get('/application-certificates/{uuid}', [CertificateController::class, 'getApplicationWithCertificates'])->name('certificates.index');
    Route::get('/certificate/preview/{uuid}', [CertificateController::class, 'getPreviewPdf'])->name('certificate-preview');
    Route::get('/certificate/original/{uuid}', [CertificateController::class, 'getOriginalPdf'])->name('certificate-original');
    Route::post('/certificate/mark-delivered/{uuid}', [CertificateController::class, 'markDelivered']);
});

//Super Admin KPI Routes
Route::middleware(['auth:sanctum', 'isSuperAdminUser'])->prefix('kpi')->group(function () {
    Route::get('active-users', [KpiController::class, 'getActiveUsers']);
    Route::get('transaction-volume', [KpiController::class, 'getTransactionVolume']);
    Route::get('api-calls', [KpiController::class, 'getApiCalls']);
    Route::get('failed-logins', [KpiController::class, 'getFailedLogins']);
});
