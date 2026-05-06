<?php

use App\Jobs\DomicilePdfJob;
use App\Models\Application;
use App\Services\PdfGenerationService;

Route::get('domicile-pdf/{application}', function (Application $application, PdfGenerationService $pdfGenerationService) {

    $paths = $pdfGenerationService->generateDomicilePdf($application, true);
    $paths['preview'] = str_replace('/storage', '', $paths['preview']);

    return response()->file(Storage::disk('public')->path($paths['preview']), [
        'Content-Type'        => 'application/pdf',
        'Content-Disposition' => 'inline; filename="Domicile.pdf"',
    ]);
});

Route::get('domicile-view/{application}', function (Application $application,  PdfGenerationService $pdfGenerationService) {
    return $pdfGenerationService->prepareDomicileHtml($application, true);
});

Route::get('ssc-pdf/{application}', function (Application $application, PdfGenerationService $pdfGenerationService) {

    $paths = $pdfGenerationService->generateSSCPdf($application, true);
    $paths['original'] = str_replace('/storage', '', $paths['original']);

    return response()->file(Storage::disk('public')->path($paths['original']), [
        'Content-Type'        => 'application/pdf',
        'Content-Disposition' => 'inline; filename="State Subject Certificate.pdf"',
    ]);
});

Route::get('ssc-view/{application}', function (Application $application,  PdfGenerationService $pdfGenerationService) {
    return $pdfGenerationService->prepareSSCHtml($application);
});
