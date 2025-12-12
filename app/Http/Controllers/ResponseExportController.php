<?php

namespace App\Http\Controllers;

use App\Exports\AllResponsesExport;
use App\Models\Response;
use App\Models\Survey;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ResponseExportController extends Controller
{
    /**
     * Export responses to Excel (supports filter by survey).
     */
    public function exportExcel(Request $request): BinaryFileResponse
    {
        $surveyId = $request->query('survey');

        if ($surveyId) {
            $survey = Survey::findOrFail($surveyId);
            $filename = 'responses_'.str($survey->title)->slug().'_'.now()->format('Y-m-d_His').'.xlsx';
        } else {
            $filename = 'all_responses_'.now()->format('Y-m-d_His').'.xlsx';
        }

        return Excel::download(new AllResponsesExport($surveyId), $filename);
    }

    /**
     * Export responses to PDF (supports filter by survey).
     */
    public function exportPdf(Request $request): HttpResponse
    {
        $surveyId = $request->query('survey');

        $query = Response::with(['survey', 'answers.question'])
            ->whereNotNull('submitted_at');

        if ($surveyId) {
            $query->where('survey_id', $surveyId);
            $survey = Survey::find($surveyId);
            $title = $survey ? "Survey Responses: {$survey->title}" : 'Survey Responses Report';
            $filename = 'responses_'.($survey ? str($survey->title)->slug() : 'survey').'_'.now()->format('Y-m-d_His').'.pdf';
        } else {
            $title = 'All Survey Responses Report';
            $filename = 'all_responses_'.now()->format('Y-m-d_His').'.pdf';
        }

        $responses = $query->get();

        // Group responses by survey for better organization
        $responsesBySurvey = $responses->groupBy('survey_id');

        // Calculate statistics
        $stats = $this->calculateStatistics($responses);

        $pdf = Pdf::loadView('exports.all-responses-pdf', [
            'responsesBySurvey' => $responsesBySurvey,
            'totalResponses' => $responses->count(),
            'stats' => $stats,
            'generatedAt' => now(),
            'title' => $title,
            'filterApplied' => $surveyId ? true : false,
        ]);

        return $pdf->setPaper('a4', 'landscape')->download($filename);
    }

    /**
     * Calculate statistics for the responses.
     */
    protected function calculateStatistics($responses): array
    {
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
            'surveyCount' => $responses->pluck('survey_id')->unique()->count(),
            'dateRange' => [
                'from' => $responses->min('submitted_at')?->format('M d, Y'),
                'to' => $responses->max('submitted_at')?->format('M d, Y'),
            ],
        ];
    }
}
