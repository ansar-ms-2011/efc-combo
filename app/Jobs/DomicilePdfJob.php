<?php

namespace App\Jobs;

use App\Models\Application;
use App\Models\ApplicationCertificate;
use App\Models\CertificateJob;
use App\Services\PdfGenerationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class DomicilePdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 180;

    public function __construct(public int $applicationId, public int $certJobId)
    {
        $this->onQueue('pdf-generation');
    }

    /**
     * @throws Throwable
     */
    public function handle(PdfGenerationService $pdfGenerationService): void
    {
        $certificateJob = CertificateJob::findOrFail($this->certJobId);
        try {
            $application = Application::findOrFail($this->applicationId);

            $certificateJob->update([
                'status' => 'processing',
            ]);

            $filePaths = $pdfGenerationService->generateDomicilePdf($application, $application->application_type_id === 2);

            // Revoke previous certificate if it exists
            if ($application->application_type_id === 2) {
                $previousCertificate = ApplicationCertificate::where('applicant_id', $application->applicant_id)->where('type', 'domicile')->get();
                foreach ($previousCertificate as $cert) {
                    $cert->update([
                        'is_revoked' => true
                    ]);
                }
            }

            $certificate = ApplicationCertificate::create([
                'application_id' => $application->id,
                'applicant_id' => $application->applicant_id,
                'type' => 'domicile',
                'is_revoked' => false,
                'certificate_number' => null,
                'pdf_path' => $filePaths['original'],
                'preview_path' => $filePaths['preview'],
                'issue_date' => now(),
            ]);

            $certificate->update([
                'certificate_number' => "D{$application->region_id}{$application->district_id}{$application->tehsil_id}{$application->center_id}{$certificate->id}"
            ]);

            $certificateJob->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

        } catch (Throwable $e) {

            $certificateJob->update([
                'completed_at' => now(),
                'status' => 'failed',
                'message' => $e->getMessage()
            ]);

            throw $e;
        }
    }
}
