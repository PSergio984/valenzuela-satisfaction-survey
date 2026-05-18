<?php

namespace App\Http\Controllers;

use App\Exports\SurveyResponsesExport;
use App\Models\Question;
use App\Models\Survey;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Notifications\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SurveyExportController extends Controller
{
    /**
     * Export survey responses to Excel (Queued).
     */
    public function exportExcel(Survey $survey): RedirectResponse
    {
        $filename = "exports/survey-{$survey->slug}-responses-".now()->format('Y-m-d-His').'.xlsx';
        $userId = auth()->id();

        Excel::queue(
            new SurveyResponsesExport($survey->id, $userId, $filename),
            $filename,
            'private'
        );

        Notification::make()
            ->title('Export Queued')
            ->body('Your export is being processed and will be available in your notifications when ready.')
            ->info()
            ->send();

        return back();
    }

    /**
     * Download an export from the private disk.
     */
    public function downloadExport(Request $request): StreamedResponse
    {
        $path = $request->query('path');

        if (! $path || ! Storage::disk('private')->exists($path)) {
            abort(404, 'Export file not found.');
        }

        return Storage::disk('private')->download($path);
    }

    /**
     * Export survey responses to PDF.
     */
    public function exportPdf(Survey $survey): Response
    {
        $survey->load(['questions' => fn ($q) => $q->orderBy('order'), 'responses.answers']);

        // Calculate statistics for each question
        $statistics = $this->calculateStatistics($survey);

        $pdf = Pdf::loadView('exports.survey-responses', [
            'survey' => $survey,
            'statistics' => $statistics,
            'generatedAt' => now(),
        ]);

        $filename = "survey-{$survey->slug}-report-".now()->format('Y-m-d').'.pdf';

        return $pdf->download($filename);
    }

    /**
     * Calculate statistics for a survey.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function calculateStatistics(Survey $survey): array
    {
        $statistics = [];

        foreach ($survey->questions as $question) {
            $questionStats = [
                'question' => $question->question,
                'type' => $question->type,
                'total_responses' => 0,
                'answers' => [],
            ];

            $allAnswers = $survey->responses->flatMap(function ($response) use ($question) {
                return $response->answers->where('question_id', $question->id);
            });

            $questionStats['total_responses'] = $allAnswers->count();

            switch ($question->type) {
                case Question::TYPE_RATING:
                    $values = $allAnswers->pluck('value')->filter()->map(fn ($v) => (int) $v);
                    $questionStats['average'] = $values->count() > 0 ? round($values->avg(), 2) : 0;
                    $questionStats['distribution'] = $values->countBy()->sortKeys()->all();
                    break;

                case Question::TYPE_RADIO:
                case Question::TYPE_SELECT:
                    $questionStats['distribution'] = $allAnswers->pluck('value')
                        ->filter()
                        ->countBy()
                        ->sortDesc()
                        ->all();
                    break;

                case Question::TYPE_CHECKBOX:
                    $allOptions = $allAnswers->flatMap(fn ($a) => $a->selected_options ?? []);
                    $questionStats['distribution'] = $allOptions->countBy()->sortDesc()->all();
                    break;

                case Question::TYPE_TEXT:
                case Question::TYPE_TEXTAREA:
                    $questionStats['sample_answers'] = $allAnswers->pluck('value')
                        ->filter()
                        ->take(5)
                        ->values()
                        ->all();
                    break;
            }

            $statistics[$question->id] = $questionStats;
        }

        return $statistics;
    }
}
