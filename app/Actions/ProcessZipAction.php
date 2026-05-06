<?php

namespace App\Actions;

use App\Models\{ApplicationCertificate, Applicant};
use Illuminate\Support\Facades\{Storage, DB, File};
use Illuminate\Support\Str;

class ProcessZipAction
{
    public function execute($zipFilePath, $userId)
    {
        $batchId = uniqid('batch_');
        $tempPath = storage_path('app/temp_processing/' . $batchId);
        $movedFiles = [];

        if (!File::exists($tempPath)) File::makeDirectory($tempPath, 0755, true);

        DB::beginTransaction();
        try {
            $zip = new \ZipArchive;
            if ($zip->open($zipFilePath) === TRUE) {
                $zip->extractTo($tempPath);
                $zip->close();
                $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($tempPath));
                // Draft Applicant fetch
                $applicant = Applicant::firstOrCreate(
                    ['identity_number' => 'DRAFT-00000'],
                    ['full_name' => 'Draft Applicant', 'identity_type' => 'local', 'status' => 1, 'created_by' => $userId]
                );

                foreach ($files as $file) {
                    if ($file->isDir()) continue;
                    $ext = strtolower($file->getExtension());
                    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'pdf'])) continue;

                    $newFileName = 'batch_' . uniqid() . '.' . $ext;
                    $dbPath = 'archived_certificates/' . $newFileName;

                    $newCert =   ApplicationCertificate::create([
                        'applicant_id' => $applicant->id,
                        'type'         => 'domicile',
                        'source'       => 'archive',
                        'certificate_number' => 'CERT-' . strtoupper(Str::random(10)),
                        'issue_date'   => now(),
                        'pdf_path'     => $dbPath,
                        'uploaded_by'  => $userId,
                    ]);
                    DB::table('application_verifications')->insert([
                        'application_certificate_id' => $newCert->id,
                        'status'       => 'pending',
                        'img_upload_by' => $userId,  
                        'data_enter_by' => null,     
                        'verified_by'  => null,
                        'remarks'      => null,
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]);

                    Storage::disk('public')->put($dbPath, file_get_contents($file->getRealPath()));
                    $movedFiles[] = $dbPath;
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            foreach ($movedFiles as $path) {
                Storage::disk('public')->delete($path);
            }
            throw $e;
        } finally {
            if (File::exists($tempPath)) File::deleteDirectory($tempPath);
        }

        return count($movedFiles);
    }
}
