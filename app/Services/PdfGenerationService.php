<?php

namespace App\Services;

use Exception;
use BaconQrCode\Writer;
use Spatie\Browsershot\Browsershot;
use App\Models\Application;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use Spatie\Browsershot\Exceptions\CouldNotTakeBrowsershot;

class PdfGenerationService
{

    public function __construct()
    {

    }
    private function generateDirectories(Application $application)
    {
        $appYearMonthDay = $application->created_at->format('Y/m/d');
        if (!file_exists(storage_path("app/public/certificates/{$appYearMonthDay}/original"))) {
            mkdir(storage_path("app/public/certificates/{$appYearMonthDay}/original"), 0777, true);
        }
        if (!file_exists(storage_path("app/public/certificates/{$appYearMonthDay}/preview"))) {
            mkdir(storage_path("app/public/certificates/{$appYearMonthDay}/preview"), 0777, true);
        }
    }

    /**
     * @throws CouldNotTakeBrowsershot
     * @throws Exception
     */
    public function generateDomicilePdf(Application $application, $duplicate = false): array
    {
        $application->load([
            'applicant.children',
            'applicant.tehsil',
            'applicant.district',
            'applicant.maritalStatus',
            'applicant.region',
            'approvals',
            'approvals.officer.district',
            'approvals.officer.tehsil',
        ]);

        $html = $this->prepareDomicileHtml($application, false, $duplicate);
        $htmlPreview = $this->prepareDomicileHtml($application, true, $duplicate);
        $appYearMonthDay = $application->created_at->format('Y/m/d');
        $this->generateDirectories($application);

        $originalPdfPath = storage_path("app/public/certificates/{$appYearMonthDay}/original/{$application->uuid}-domicile.pdf");
        $previewPdfPath = storage_path("app/public/certificates/{$appYearMonthDay}/preview/{$application->uuid}-domicile.pdf");

        $this->saveBrowserShot($html, $originalPdfPath);
        $this->saveBrowserShot($htmlPreview, $previewPdfPath);

        return [
            'original' => "/storage/certificates/{$appYearMonthDay}/original/{$application->uuid}-domicile.pdf",
            'preview' => "/storage/certificates/{$appYearMonthDay}/preview/{$application->uuid}-domicile.pdf",
        ];
    }

    public function prepareDomicileHtml($application, $preview = false, $duplicate = false): string
    {
        $qrCode = $this->generateQrCode($application);

        //Prepare Logo and QR Code
        $qrPath = public_path('images/qr.png');
        $qrType = pathinfo($qrPath, PATHINFO_EXTENSION);
        $qrData = file_get_contents($qrPath);

        $qr = 'data:image/' . $qrType . ';base64,' . base64_encode($qrData);

        

        $logoPath = public_path('images/efc-logo.png');
        $logoType = pathinfo($logoPath, PATHINFO_EXTENSION);
        $logoData = file_get_contents($logoPath);

        $logo = 'data:image/' . $logoType . ';base64,' . base64_encode($logoData);

        //Prepare Personal Image
        if ($application->personal_image) {
            $localPath = storage_path('app/public/' . str_replace(url('/storage/'), '', $application->personal_image));
            if (file_exists($localPath)) {
                $imageData = file_get_contents($localPath);
                $image = 'data:image/' . pathinfo($localPath, PATHINFO_EXTENSION) . ';base64,' . base64_encode($imageData);
            } else {
                $image = null;
            }
        } else {
            $image = null;
        }

        $approvalContext = $this->getApprovalContext($application);


        return view('pdf-templates.domicile', array_merge(compact('application', 'qr', 'logo', 'image', 'qrCode', 'preview', 'duplicate'), $approvalContext))->render();
    }

    /**
     * @throws CouldNotTakeBrowsershot
     */
    public function generateSSCPdf(Application $application, $duplicate): array
    {
        $application->load([
            'applicant.children',
            'applicant.tehsil',
            'applicant.district',
            'applicant.region',
            'applicant.maritalStatus',
            'applicant.biometrics',
            'biometrics',
            'approvals',
            'approvals.officer.district',
            'approvals.officer.tehsil',
        ]);
        $html = $this->prepareSSCHtml($application, false, $duplicate);
        $htmlPreview = $this->prepareSSCHtml($application, true, $duplicate);
        $appYearMonthDay = $application->created_at->format('Y/m/d');
        $this->generateDirectories($application);

        $originalPdfPath = storage_path("app/public/certificates/{$appYearMonthDay}/original/{$application->uuid}-ssc.pdf");
        $previewPdfPath = storage_path("app/public/certificates/{$appYearMonthDay}/preview/{$application->uuid}-ssc.pdf");

        $this->saveBrowserShot($html, $originalPdfPath);
        $this->saveBrowserShot($htmlPreview, $previewPdfPath);

        return [
            'original' => "/storage/certificates/{$appYearMonthDay}/original/{$application->uuid}-ssc.pdf",
            'preview' => "/storage/certificates/{$appYearMonthDay}/preview/{$application->uuid}-ssc.pdf",
        ];
    }

    public function prepareSSCHtml($application, $preview = false, $duplicate=false): string
    {
        $qrCode = $this->generateQrCode($application, 'ssc');
        //Prepare Logo and QR Code
        $headerPath = public_path('images/state-subject-logo.png');
        $headerType = pathinfo($headerPath, PATHINFO_EXTENSION);
        $headerData = file_get_contents($headerPath);

        $header = 'data:image/' . $headerType . ';base64,' . base64_encode($headerData);

        $qrPath = public_path('images/qr.png');
        $qrType = pathinfo($qrPath, PATHINFO_EXTENSION);
        $qrData = file_get_contents($qrPath);

        $qr = 'data:image/' . $qrType . ';base64,' . base64_encode($qrData);

        

        $logoPath = public_path('images/efc-logo.png');
        $logoType = pathinfo($logoPath, PATHINFO_EXTENSION);
        $logoData = file_get_contents($logoPath);

        $logo = 'data:image/' . $logoType . ';base64,' . base64_encode($logoData);

        //Prepare Personal Image
        if ($application->personal_image) {
            $localPath = storage_path('app/public/' . str_replace(url('/storage/'), '', $application->personal_image));
            if (file_exists($localPath)) {
                $imageData = file_get_contents($localPath);
                $image = 'data:image/' . pathinfo($localPath, PATHINFO_EXTENSION) . ';base64,' . base64_encode($imageData);
            } else {
                $image = null;
            }
        } else {
            $image = null;
        }

        $approvalContext = $this->getApprovalContext($application); 

        $biometrics = $application->applicant->biometrics->keyBy('finger_type');
        $fingers = ['thumb', 'index', 'middle', 'ring', 'little'];
        $fingerprints = [];

        foreach ($fingers as $finger) {
            if (!empty($biometrics[$finger])) {
                $url = $biometrics[$finger]->image_path;

                // Local path banaye agar storage me hai
                $localPath = storage_path('app/public/' . str_replace(url('/storage/'), '', $url));

                if (file_exists($localPath)) {
                    $fingerprints[$finger] = 'data:image/' . pathinfo($localPath, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($localPath));
                } else {
                    $fingerprints[$finger] = $url; // fallback: direct URL
                }
            } else {
                $fingerprints[$finger] = null;
            }
        }

        return view('pdf-templates.state-subject-certificate', array_merge(compact('application', 'qr', 'logo', 'image', 'header', 'qrCode', 'fingerprints', 'preview', 'duplicate'), $approvalContext))
            ->render();
    }

    private function generateQrCode($application, $type = 'domicile')
    {
        $url = url(config('app.qr_code_base_url') . "/{$type}-pdf/$application->uuid");
        $renderer = new ImageRenderer(
            new RendererStyle(200), // size
            new SvgImageBackEnd()
        );
        // ✅ Writer
        $writer = new Writer($renderer);
        // ✅ Generate QR SVG
        return $writer->writeString($url);
    }

    /**
     * @throws CouldNotTakeBrowsershot
     * @throws Exception
     */
    private function saveBrowserShot($html, $outputPath): void
    {
        $chromePath = config('app.chrome_path');

        if ($chromePath) {
            ini_set('memory_limit', '512M');
            Browsershot::html($html)
                ->setChromePath($chromePath) // Windows
                ->setNodeBinary('node')
                ->setNpmBinary('npm')
                ->format('A4')
                ->margins(0, 0, 0, 0, 'mm')
                ->showBackground()
                ->waitUntilNetworkIdle()
                ->setOption('args', [
                    '--disable-dev-shm-usage',
                    '--disable-web-security',
                ])->save($outputPath);
        } else {
            throw new Exception('Chrome path not found for BrowserShot');
        }
    }

    public function getApprovalContext(Application $application): array
{
    $approvals = $application->approvals;

    $dcApproval = $approvals->first(fn ($a) => $a->level === 'DC');
    $acApproval = $approvals->first(fn ($a) => $a->level === 'AC');
    $acrApproval = $approvals->first(fn ($a) => $a->level === 'ACR');

    return [
        'dcApproval' => $dcApproval,
        'acApproval' => $acApproval,
        'acrApproval' => $acrApproval,

        'acName' => $acApproval?->officer?->name,
        'acrName' => $acrApproval?->officer?->name,

        'signdate' => $acApproval?->sign_date?->format('d-m-Y') 
            ?? $acrApproval?->sign_date?->format('d-m-Y') 
            ?? null,

        'districtName' => $dcApproval?->officer?->district?->urdu_name,

        'tehsilName' => $acApproval?->officer?->tehsil?->urdu_name
            ?? $acrApproval?->officer?->tehsil?->urdu_name,
    ];
}
}
