<?php

declare(strict_types=1);

use function Pest\Laravel\get;

it('renders the QR code modal with copy button', function () {
    $survey = \App\Models\Survey::factory()->create([
        'slug' => 'test-survey',
    ]);
    $surveyUrl = url('/survey/' . $survey->slug);
    $qrCode = '<svg></svg>';
    $qrCodeRaw = '<svg></svg>';

    $view = view('filament.modals.qr-code', [
        'survey' => $survey,
        'surveyUrl' => $surveyUrl,
        'qrCode' => $qrCode,
        'qrCodeRaw' => $qrCodeRaw,
    ]);

    $html = $view->render();
    expect($html)->toContain('Copy')
        ->toContain($surveyUrl)
        ->toContain('Download')
        ->toContain('Open Survey');
});
