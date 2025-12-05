<?php

namespace App\Services;

use App\Models\Survey;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class QrCodeService
{
    /**
     * Generate a QR code SVG for a survey.
     */
    public function generateSvg(Survey $survey, int $size = 300): string
    {
        $url = route('surveys.show', $survey);

        $renderer = new ImageRenderer(
            new RendererStyle($size),
            new SvgImageBackEnd
        );

        $writer = new Writer($renderer);

        return $writer->writeString($url);
    }

    /**
     * Generate a QR code and return as base64 data URI.
     */
    public function generateDataUri(Survey $survey, int $size = 300): string
    {
        $svg = $this->generateSvg($survey, $size);

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    /**
     * Get the public URL for a survey.
     */
    public function getSurveyUrl(Survey $survey): string
    {
        return route('surveys.show', $survey);
    }
}
