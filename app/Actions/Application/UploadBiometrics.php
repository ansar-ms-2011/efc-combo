<?php

namespace App\Actions\Application;

use App\Models\ApplicationBiometric;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadBiometrics
{
    public function execute(array $biometrics, int $applicationId, int $applicantId, string $appYearMonthDay): void
    {
        $fingers = ['thumb', 'index', 'middle', 'ring', 'little'];
        $dirPath = "applications/{$appYearMonthDay}/biometrics";

        DB::transaction(function () use ($biometrics, $applicationId, $applicantId, $fingers, $dirPath) {

            foreach ($fingers as $finger) {

                $base64Image = data_get($biometrics, "{$finger}.image_file");
                $featureSet  = data_get($biometrics, "{$finger}.feature_set");
                $imagePath   = data_get($biometrics, "{$finger}.image_path");

                // Skip if no new image provided
                if (!$base64Image) {
                    continue;
                }

                $imageData = extractImageData($base64Image);

                if (!$imageData) {
                    throw new \Exception("Invalid base64 image for {$finger}");
                }

                $biometric = ApplicationBiometric::updateOrCreate(
                    [
                        'applicant_id'   => $applicantId,
                        'application_id' => $applicationId,
                        'finger_type'    => $finger,
                    ],
                    [
                        'feature_set' => $featureSet,
                        'image_path'  => $imagePath,
                    ]
                );

                // Delete old image BEFORE saving new one
                if ($biometric->image_path) {
                    Storage::disk('public')->delete($biometric->image_path);
                }

                $newPath = $this->storeImage($imageData, $dirPath);

                $biometric->update([
                    'image_path' => $newPath
                ]);
            }
        });
    }
    private function storeImage(array $imageData, string $dirPath): string
    {
        $filename = Str::uuid() . '.' . $imageData['extension'];
        $fullPath = "{$dirPath}/{$filename}";

        Storage::disk('public')->put($fullPath, $imageData['data']);

        return $fullPath;
    }
}
