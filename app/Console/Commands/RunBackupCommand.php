<?php

namespace App\Console\Commands;

use App\Jobs\CreateBackupJob;
use App\Models\Backup;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class RunBackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backups:queue {--type=manual : Backup type (monthly|yearly|manual)} {--scope=full_site : Scope (monthly|yearly|full_site)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a backup record and dispatch queue job';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $payload = [
            'type' => $this->option('type'),
            'scope' => $this->option('scope'),
        ];

        $validator = Validator::make($payload, [
            'type' => ['required', 'in:monthly,yearly,manual'],
            'scope' => ['required', 'in:monthly,yearly,full_site'],
        ]);

        if ($validator->fails()) {
            $this->error($validator->errors()->first());

            return self::FAILURE;
        }

        $backup = Backup::create([
            'type' => $payload['type'],
            'scope' => $payload['scope'],
            'status' => 'pending',
            'progress_percentage' => 0,
        ]);

        CreateBackupJob::dispatch($backup->id)->onQueue((string) config('backup_system.queue', 'default'));

        $this->info("Backup queued successfully. Backup ID: {$backup->id}");

        return self::SUCCESS;
    }
}
