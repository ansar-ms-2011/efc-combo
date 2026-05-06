<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use App\Actions\ProcessZipAction;


class ProcessArchivedZipJob implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;


    // Create a new job instance.
    protected $fullPath;
    protected $userId;

    public function __construct($fullPath, $userId)
    {
        $this->fullPath = $fullPath;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(ProcessZipAction $action): void
    {
        try {
            $action->execute($this->fullPath, $this->userId);
            if (File::exists($this->fullPath)) File::delete($this->fullPath);
        } catch (\Exception $e) {
            if (File::exists($this->fullPath)) File::delete($this->fullPath);
            \Log::error('Error in ProcessArchivedZipJob: ' . $e->getMessage());
        }
    }
}
