<?php

namespace App\Http\Controllers;

use App\Mail\ApplicationStatusMail;
use App\Models\Application;
use App\Models\ApplicationCertificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    public function generateCertificate(ApplicationCertificate $certificate)
    {
        return response()->json($certificate);
    }

    public function getPreviewPdf(string $uuid)
    {
        $certificate = ApplicationCertificate::where('uuid', $uuid)->firstOrFail();
        $path = $certificate->preview_path;
        $path = str_replace('/storage', '', $path);
        $path = Storage::disk('public')->path($path);
        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="certificate-preview.pdf"', // Crucial for print
        ]);
    }

    public function getOriginalPdf(string $uuid)
    {
        $certificate = ApplicationCertificate::where('uuid', $uuid)->firstOrFail();

        $path = $certificate->pdf_path;
        $certificate->increment('print_count');

        //Add to workflow
        $certificate->application->workFlows()->create([
            'from_status' => null,
            'to_status' => null,
            'action' => 'updated',
            'remarks' => 'Original certificate was fetched for printing',
            'created_by' => auth()->id() ?? 1,
        ]);

        $path = str_replace('/storage', '', $path);
        $path = Storage::disk('public')->path($path);
        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="certificate-original.pdf"', // Crucial for print
        ]);
    }

 public function markDelivered($uuid)
{
    DB::beginTransaction();

    try {
        $certificate = ApplicationCertificate::where('uuid', $uuid)->firstOrFail();
        $app = $certificate->application;

        if($certificate->application->current_status !== 'delivered'){
            $previousStatus = $certificate->application->current_status;
            $certificate->application()->update([
                'current_status' => 'delivered'
            ]);
            $certificate->application->workFlows()->create([
                'from_status' => $previousStatus,
                'to_status' => 'delivered',
                'action' => 'updated',
                'remarks' => 'Application was marked as delivered',
                'created_by' => auth()->id() ?? 1,
            ]);
        }

        $user = auth()->user();

        DB::commit();

        // ✅ After commit email send
        DB::afterCommit(function () use ($app, $user) {

            $email = $app->applicant->email ?? null;
            if (!$email) return;

            $statusMap = [
                'delivered' => 'Delivered'
            ];

            Mail::to($email)->queue(
                new ApplicationStatusMail(
                    $app->load('applicant'),
                    $user->roles[0]->name ?? 'Officer',
                    $statusMap['delivered']
                )
            );
        });

        return response()->json(['success' => true]);

    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }
}

    public function getApplicationWithCertificates(string $uuid)
    {
        $application = Application::where('uuid', $uuid)->firstOrFail();
        $application->load('certificates');
        return response()->json($application);
    }
}
