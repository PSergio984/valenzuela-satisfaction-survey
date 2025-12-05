<?php

namespace App\Services;

use App\Models\Response;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ResponseExportService
{
    /**
     * Transform a response model into an exportable array.
     *
     * @return array<string, mixed>
     */
    public function transformResponse(Response $response): array
    {
        $response->loadMissing(['survey', 'answers.question']);

        $data = [
            'ID' => $response->id,
            'Survey' => $response->survey->title ?? 'N/A',
            'Respondent Name' => $response->respondent_name ?? 'Anonymous',
            'Respondent Email' => $response->respondent_email ?? '',
            'Respondent Phone' => $response->respondent_phone ?? '',
            'IP Address' => $response->ip_address ?? '',
            'Submitted At' => $response->submitted_at?->format('Y-m-d H:i:s') ?? '',
            'Created At' => $response->created_at?->format('Y-m-d H:i:s') ?? '',
        ];

        // Add answers as columns
        foreach ($response->answers as $answer) {
            $questionLabel = $answer->question->question ?? "Question {$answer->question_id}";
            $questionLabel = str()->limit($questionLabel, 50);

            if ($answer->selected_options) {
                $data[$questionLabel] = is_array($answer->selected_options)
                    ? implode(', ', $answer->selected_options)
                    : $answer->selected_options;
            } else {
                $data[$questionLabel] = $answer->value ?? '';
            }
        }

        return $data;
    }

    /**
     * Transform multiple responses for export.
     *
     * @param  Collection<int, Response>  $responses
     * @return array<int, array<string, mixed>>
     */
    public function transformResponses(Collection $responses): array
    {
        return $responses->map(fn (Response $response) => $this->transformResponse($response))->toArray();
    }

    /**
     * Get standardized headers for all responses in the collection.
     *
     * @param  Collection<int, Response>  $responses
     * @return array<int, string>
     */
    public function getStandardizedHeaders(Collection $responses): array
    {
        $baseHeaders = [
            'ID',
            'Survey',
            'Respondent Name',
            'Respondent Email',
            'Respondent Phone',
            'IP Address',
            'Submitted At',
            'Created At',
        ];

        // Collect all unique question headers from all responses
        $questionHeaders = [];
        foreach ($responses as $response) {
            if (method_exists($response, 'loadMissing')) {
                $response->loadMissing(['answers.question']);
            }
            foreach ($response->answers as $answer) {
                $questionLabel = $answer->question->question ?? "Question {$answer->question_id}";
                $questionLabel = str()->limit($questionLabel, 50);
                if (! in_array($questionLabel, $questionHeaders)) {
                    $questionHeaders[] = $questionLabel;
                }
            }
        }

        return array_merge($baseHeaders, $questionHeaders);
    }

    /**
     * Normalize a row to include all headers with empty values for missing keys.
     *
     * @param  array<string, mixed>  $row
     * @param  array<int, string>  $headers
     * @return array<string, mixed>
     */
    public function normalizeRow(array $row, array $headers): array
    {
        $normalized = [];
        foreach ($headers as $header) {
            $normalized[$header] = $row[$header] ?? '';
        }

        return $normalized;
    }

    /**
     * Export responses to Excel format and stream the download.
     *
     * @param  Collection<int, Response>  $responses
     */
    public function exportToExcel(Collection $responses, string $filename = 'responses.xlsx'): StreamedResponse
    {
        $headers = $this->getStandardizedHeaders($responses);
        $rows = $this->transformResponses($responses);

        return response()->streamDownload(function () use ($rows, $headers) {
            $writer = SimpleExcelWriter::streamDownload('export.xlsx')
                ->noHeaderRow()
                ->addRow($headers);

            foreach ($rows as $row) {
                $writer->addRow($this->normalizeRow($row, $headers));
            }

            $writer->close();
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Export responses to CSV format and stream the download.
     *
     * @param  Collection<int, Response>  $responses
     */
    public function exportToCsv(Collection $responses, string $filename = 'responses.csv'): StreamedResponse
    {
        $headers = $this->getStandardizedHeaders($responses);
        $rows = $this->transformResponses($responses);

        return response()->streamDownload(function () use ($rows, $headers) {
            $writer = SimpleExcelWriter::streamDownload('export.csv')
                ->noHeaderRow()
                ->addRow($headers);

            foreach ($rows as $row) {
                $writer->addRow($this->normalizeRow($row, $headers));
            }

            $writer->close();
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Export responses to PDF format and stream the download.
     *
     * @param  Collection<int, Response>  $responses
     */
    public function exportToPdf(Collection $responses, string $filename = 'responses.pdf'): \Illuminate\Http\Response
    {
        $headers = $this->getStandardizedHeaders($responses);
        $rows = $this->transformResponses($responses);

        // Get survey title for the report
        $surveyTitle = $responses->first()?->survey?->title ?? 'Survey Responses';

        // Calculate some statistics
        $stats = $this->calculateStatistics($responses);

        $pdf = Pdf::loadView('exports.responses-pdf', [
            'title' => $surveyTitle,
            'headers' => $headers,
            'rows' => array_map(fn ($row) => $this->normalizeRow($row, $headers), $rows),
            'stats' => $stats,
            'generatedAt' => now()->format('F j, Y \a\t g:i A'),
            'totalResponses' => $responses->count(),
        ]);

        return $pdf->setPaper('a4', 'landscape')
            ->download($filename);
    }

    /**
     * Calculate statistics for the responses.
     *
     * @param  Collection<int, Response>  $responses
     * @return array<string, mixed>
     */
    public function calculateStatistics(Collection $responses): array
    {
        // Load relationships if this is an Eloquent collection
        if (method_exists($responses, 'loadMissing')) {
            $responses->loadMissing(['answers.question']);
        }

        $ratingAnswers = [];
        foreach ($responses as $response) {
            foreach ($response->answers as $answer) {
                if ($answer->question?->type === 'rating' && is_numeric($answer->value)) {
                    $questionLabel = $answer->question->question ?? "Question {$answer->question_id}";
                    if (! isset($ratingAnswers[$questionLabel])) {
                        $ratingAnswers[$questionLabel] = [];
                    }
                    $ratingAnswers[$questionLabel][] = (float) $answer->value;
                }
            }
        }

        $averageRatings = [];
        foreach ($ratingAnswers as $question => $values) {
            $averageRatings[$question] = round(array_sum($values) / count($values), 2);
        }

        return [
            'averageRatings' => $averageRatings,
            'overallAverage' => ! empty($averageRatings)
                ? round(array_sum($averageRatings) / count($averageRatings), 2)
                : null,
            'dateRange' => [
                'from' => $responses->min('submitted_at')?->format('M d, Y'),
                'to' => $responses->max('submitted_at')?->format('M d, Y'),
            ],
        ];
    }

    /**
     * Generate a filename for the export.
     */
    public function generateFilename(string $prefix, string $extension): string
    {
        $timestamp = now()->format('Y-m-d_His');

        return "{$prefix}_{$timestamp}.{$extension}";
    }
}
