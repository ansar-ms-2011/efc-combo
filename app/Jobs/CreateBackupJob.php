<?php

namespace App\Jobs;

use App\Models\Backup;
use App\Services\BackupService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;

class CreateBackupJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 0;
    public int $timeout;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $backupId)
    {
        $this->timeout = (int) config('backup_system.process_timeout', 3600) + 120;
        $this->onQueue((string) config('backup_system.queue', 'default'));
    }

    /**
     * Prevent parallel backup execution.
     */
    public function middleware(): array
    {
        return [
            (new WithoutOverlapping('global-backup-processing'))
                ->releaseAfter(30)
                ->expireAfter(((int) config('backup_system.process_timeout', 3600)) + 300),
        ];
    }

    /**
     * Execute the job.
     */
    public function handle(BackupService $backupService): void
    {
        $backup = Backup::find($this->backupId);

        if (!$backup || $backup->status === 'completed') {
            return;
        }

        $backup->update([
            'status' => 'processing',
            'started_at' => now(),
            'progress_percentage' => max((int) $backup->progress_percentage, 1),
            'error_message' => null,
        ]);

        try {
            $filePath = $backupService->create($backup);

            $backup->update([
                'status' => 'completed',
                'file_path' => $filePath,
                'progress_percentage' => 100,
                'completed_at' => now(),
            ]);
        } catch (\Throwable $exception) {
            $backup->update([
                'status' => 'failed',
                'progress_percentage' => 0,
                'error_message' => $exception->getMessage(),
                'completed_at' => now(),
            ]);

            throw $exception;
        }
    }
}
