<?php

namespace App\Http\Controllers;

use App\Jobs\CreateBackupJob;
use App\Models\Backup;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizePermission($request, 'backups.view');

        $backups = Backup::query()
            ->latest('id')
            ->paginate(20);

        return response()->json($backups);
    }

    public function store(Request $request)
    {
        $this->authorizePermission($request, 'backups.create');

        $validated = $request->validate([
            'scope' => ['nullable', 'in:monthly,yearly,full_site,custom'],
            'start_date' => ['required_if:scope,custom', 'date'],
            'end_date' => ['required_if:scope,custom', 'date', 'after_or_equal:start_date'],
        ]);

        // Count today's manual backups
        $todayCount = Backup::where('type', 'manual')
            ->whereDate('created_at', Carbon::today())
            ->count();

        if ($todayCount >= 2) {
            return response()->json([
                'message' => 'You can only create 2 backups per day.'
            ], 429); // Too Many Requests
        }

        $backup = Backup::create([
            'type' => 'manual',
            'scope' => $validated['scope'] ?? 'full_site',
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'status' => 'pending',
            'progress_percentage' => 0,
        ]);

        CreateBackupJob::dispatch($backup->id)->onQueue((string)config('backup_system.queue', 'default'));

        return response()->json([
            'message' => 'Backup has been queued successfully.',
            'data' => $backup,
        ], 202);
    }

    public function show(Request $request, Backup $backup)
    {
        $this->authorizePermission($request, 'backups.view');

        return response()->json(['data' => $backup]);
    }

    public function download(Request $request, Backup $backup)
    {
        if ($backup->status !== 'completed' || empty($backup->file_path)) {
            return response()->json(['message' => 'Backup file is not ready for download.'], 422);
        }

        $disk = Storage::disk(config('backup_system.disk', 'backup'));

        if (!$disk->exists($backup->file_path)) {
            return response()->json(['message' => 'Backup file not found.'], 404);
        }

        $filename = basename($backup->file_path);
        $filePath = $disk->path($backup->file_path);
        $mimeType = $disk->mimeType($backup->file_path) ?: 'application/gzip';

        return response()->stream(function() use ($filePath) {
            $stream = fopen($filePath, 'r');
            fpassthru($stream);
            fclose($stream);
        }, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'attachment; filename="' . $filename . '"; filename*=UTF-8\'\'' . rawurlencode($filename),
            'Content-Length' => $disk->size($backup->file_path),
        ]);
    }

    private function authorizePermission(Request $request, string $permission): void
    {
        abort_unless($request->user() && $request->user()->can($permission), 403, 'Unauthorized');
    }
}
