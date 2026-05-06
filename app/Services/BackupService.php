<?php

namespace App\Services;

use App\Models\Backup;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Spatie\DbDumper\Compressors\GzipCompressor;
use Symfony\Component\Process\Process;

class BackupService
{
    public function create(Backup $backup): string
    {
        $diskName = (string)config('backup_system.disk', 'backup');
        $scopeDirectory = $backup->scope === 'full_site' ? 'fullsite' : $backup->scope;

        $existingFiles = $this->listBackupFiles($diskName);
        $this->configurePackage($diskName, $scopeDirectory, $backup);

        $backup->update(['progress_percentage' => 25]);

        $params = [
            '--disable-notifications' => true,
        ];

        // If no files are included for the specific scope, only backup the database
        $includePaths = config('backup.backup.source.files.include');
        if (empty($includePaths)) {
            $params['--only-db'] = true;
        }

        $params['--filename'] = $scopeDirectory . '-' . now()->format('Y-m-d-H-i-s') . '.gz';

        $exitCode = Artisan::call('backup:run', $params);

        if ($exitCode !== 0) {
            throw new RuntimeException('Spatie backup:run failed: ' . trim(Artisan::output()));
        }

        $backup->update(['progress_percentage' => 90]);

        $newFile = $this->detectCreatedArchive($diskName, $existingFiles);
        if (!$newFile) {
            throw new RuntimeException('Backup completed but no archive was found on destination disk.');
        }

        return $newFile;
    }

    private function getPathsForDateRange($startDate, $endDate): array
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $paths = [];

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $path = storage_path('app/public/applications/' . $date->format('Y/m/d'));
            if (is_dir($path)) {
                $paths[] = $path;
            }

            $path = storage_path('app/public/certificates/' . $date->format('Y/m/d'));
            if (is_dir($path)) {
                $paths[] = $path;
            }
        }
        return array_unique($paths);
    }

    private function configurePackage(string $diskName, string $scopeDirectory, Backup $backup): void
    {
        $connection = (string)config('database.default', 'mysql');
        $dumpBinary = $this->resolveMysqldumpPath();

        if (!$dumpBinary) {
            throw new RuntimeException('mysqldump not found. Set BACKUP_MYSQLDUMP_PATH in .env, e.g. /Applications/ServBay/bin/mysqldump');
        }

        $includePaths = [];

        if ($backup->scope === 'monthly') {
            $path = storage_path('app/public/applications/' . now()->format('Y/m'));
            if (is_dir($path)) $includePaths[] = $path;
            $path = storage_path('app/public/certificates/' . now()->format('Y/m'));
            if (is_dir($path)) $includePaths[] = $path;
        } elseif ($backup->scope === 'yearly') {
            $path = storage_path('app/public/applications/' . now()->format('Y'));
            if (is_dir($path)) $includePaths[] = $path;
            $path = storage_path('app/public/certificates/' . now()->format('Y'));
            if (is_dir($path)) $includePaths[] = $path;
        } elseif ($backup->scope === 'custom') {
            $startDate = $backup->start_date ?? null;
            $endDate = $backup->end_date ?? null;
            if ($startDate && $endDate) {
                $includePaths = $this->getPathsForDateRange($startDate, $endDate);
                \Log::info('Custom backup paths: ' . implode(', ', $includePaths));
            }
        }else{
            $includePaths = [storage_path('app/public')];
        }

        // Ensure all paths are real paths and exist
        $includePaths = array_map(function ($path) {
            return realpath($path) ?: $path;
        }, $includePaths);

        config([
            'backup.backup.name' => $scopeDirectory,
            'backup.backup.source.databases' => [$connection],
            'backup.backup.source.files.include' => $includePaths,
            'backup.backup.source.files.follow_links' => true,
            'backup.backup.source.files.relative_path' => base_path(),
        ]);

        if (config('backup.backup.database_dump_compressor') === GzipCompressor::class) {
            config([
                'backup.backup.database_dump_compressor' => GzipCompressor::class,
                'backup.backup.database_dump_file_extension' => 'sql',
            ]);
        }

        config([
            'backup.backup.destination.disks' => [$diskName],
            'backup.backup.destination.filename_prefix' => '',
            'backup.backup.temporary_directory' => storage_path('app/tmp/backups'),
            "database.connections.{$connection}.dump.dump_binary_path" => is_dir($dumpBinary) ? $dumpBinary : dirname($dumpBinary),
            "database.connections.{$connection}.dump.use_single_transaction" => true,
            "database.connections.{$connection}.dump.timeout" => (int)config('backup_system.process_timeout', 3600),
        ]);
    }

    private function resolveMysqldumpPath(): ?string
    {
        $configured = trim((string)config('backup_system.mysql_dump_path', ''));
        if ($configured !== '' && file_exists($configured)) {
            return $configured;
        }

        if (PHP_OS_FAMILY === 'Windows') {
            $process = Process::fromShellCommandline('where mysqldump');
        } else {
            // Use 'which' as it's more portable for many shells and environments, or 'command -v'
            $process = Process::fromShellCommandline('which mysqldump');
        }
        $process->setTimeout(10);
        $process->run();

        if (!$process->isSuccessful()) {
            return null;
        }

        $binary = trim($process->getOutput());
        if ($binary === '') {
            return null;
        }

        // 'where' on Windows might return multiple paths separated by newlines
        if (PHP_OS_FAMILY === 'Windows') {
            $lines = explode(PHP_EOL, $binary);
            $binary = trim($lines[0]);
        }

        return $binary !== '' ? $binary : null;
    }

    private function listBackupFiles(string $diskName): array
    {
        $files = Storage::disk($diskName)->allFiles();
        $allowedExtensions = ['.zip', '.gz'];

        return array_values(array_filter($files, function (string $file) use ($allowedExtensions) {
            $lowercaseFile = strtolower($file);
            foreach ($allowedExtensions as $extension) {
                if (str_ends_with($lowercaseFile, $extension)) {
                    return true;
                }
            }

            return false;
        }));
    }

    private function detectCreatedArchive(string $diskName, array $existingFiles): ?string
    {
        $currentFiles = $this->listBackupFiles($diskName);
        $newFiles = array_values(array_diff($currentFiles, $existingFiles));

        if (!empty($newFiles)) {
            rsort($newFiles);

            return $newFiles[0];
        }

        if (!empty($currentFiles)) {
            rsort($currentFiles);

            return $currentFiles[0];
        }

        return null;
    }
}
