<?php

namespace App\Http\Controllers;

use App\Jobs\DomicilePdfJob;
use App\Jobs\StateSubjectPdfJob;
use App\Models\CertificateJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CertificateJobsController extends Controller
{
    public function index()
    {
        $jobs = CertificateJob::with([
            'application.applicant',
            'application.appointment'
        ])
            ->latest()
            ->paginate(15);

        // Transform the paginated collection
        $jobs->getCollection()->transform(function ($job) {
            $duration = null;
            if ($job->started_at && $job->completed_at) {
                $start = Carbon::parse($job->started_at);
                $end = Carbon::parse($job->completed_at);

                // Absolute difference in seconds
                $diffSeconds = abs($end->diffInSeconds($start));

                $minutes = floor($diffSeconds / 60);
                $seconds = $diffSeconds % 60;

                $duration = "{$minutes}m {$seconds}s";
            }

            return [
                'id' => $job->id,
                'application_id' => $job->application_id,
                'tracking_token_no' => $job->application->tracking_token_no ?? null,
                'token' => $job->application->appointment->qmatic_token ?? null,
                'applicant_name' => $job->application->applicant->full_name ?? '',
                'identity_number' => $job->application->applicant->identity_number ?? '',
                'status' => $job->status,
                'message' => $job->message,
                'created_at' => $job->application->created_at,
                'started_at' => $job->started_at,
                'completed_at' => $job->completed_at,
                'duration' => $duration,
            ];
        });

        return response()->json($jobs);
    }

    public function reInitiateJob(string $jobId)
    {
        $job = CertificateJob::find($jobId);
        $application = $job->application;
        $result = DB::transaction(function () use ($job, $application) {
            if ($job->type === 'state') {
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
            if ($job->type === 'domicile') {
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
            $job->update([
                're_initiated_at' => now(),
                're_initiated_by' => auth()->id(),
                're_initiated' => true,
                'status' => 're-initiated',
            ]);
        });
        return response()->json($result);
    }
}
