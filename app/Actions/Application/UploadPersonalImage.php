<?php

namespace App\Actions\Application;

use App\Models\Application;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadPersonalImage
{
    /**
     * @throws Exception
     */
    public function execute($base64Image, int $applicationId, string $appYearMonthDay)
    {
        $path = "applications/{$appYearMonthDay}/applicant_images";
        $disk = 'public';

        if (!$base64Image) return;

        $application = Application::findOrFail($applicationId);

        if ($application->personal_image) {
            Storage::disk('public')->delete($application->personal_image);
        }

        $imageData = extractImageData($base64Image);

        if (!$imageData) {
            throw new Exception('Invalid base64 image');
        }

        // Generate unique filename
        $extension = $imageData['extension'];
        $filename = Str::uuid() . '.' . $extension;
        $fullPath = $path . '/' . $filename;

        // Store the file
        Storage::disk($disk)->put($fullPath, $imageData['data']);
        $application->personal_image = $fullPath;
        $application->saveQuietly();

        return $fullPath;
    }
}
