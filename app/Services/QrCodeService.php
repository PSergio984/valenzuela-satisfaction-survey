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
        $svg = $writer->writeString($url);

        // Remove the XML declaration for inline SVG rendering in HTML
        $svg = preg_replace('/^<\?xml[^>]*\?>\s*/i', '', $svg);

        // Make SVG responsive by replacing fixed width/height with 100%
        $svg = preg_replace('/(<svg[^>]*)\s+width="[^"]*"/', '$1 width="100%"', $svg);
        $svg = preg_replace('/(<svg[^>]*)\s+height="[^"]*"/', '$1 height="100%"', $svg);

        return $svg;
    }

    /**
     * Generate the raw SVG (with XML declaration) for downloads.
     */
    public function generateRawSvg(Survey $survey, int $size = 300): string
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
        $svg = $this->generateRawSvg($survey, $size);

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }

    /**
     * Get the public URL for a survey.
     */
    public function getSurveyUrl(Survey $survey): string
    {
        return route('surveys.show', $survey);
    }
}
