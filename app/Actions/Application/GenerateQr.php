<?php

namespace App\Actions\Application;

use App\Models\Application;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Storage;

class GenerateQr
{
    public function execute(Application $application, string $appYearMonthDay)
    {

        if (!$application->uuid || !$appYearMonthDay) return null;

        // ✅ Create renderer
        $renderer = new ImageRenderer(
            new RendererStyle(200), // size
            new SvgImageBackEnd()
        );

        // ✅ Writer
        $writer = new Writer($renderer);

        // ✅ Generate QR SVG
        $svg = $writer->writeString($application->uuid);

        $path = "applications/{$appYearMonthDay}/qr_codes/{$application->uuid}.svg";

        Storage::disk('public')->put($path, $svg);

        $application->qr_code_url = $path;
        $application->saveQuietly();

        return $path;
    }
}
