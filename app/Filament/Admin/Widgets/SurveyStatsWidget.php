<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Question;
use App\Models\Response;
use App\Models\Survey;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\Widget as BaseWidget;
use Illuminate\Support\Facades\DB;

class SurveyStatsWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected string $view = 'filament.admin.widgets.survey-stats-widget';

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;

        // Base queries
        $surveyQuery = Survey::query();
        $responseQuery = Response::query();

        if ($startDate) {
            $surveyQuery->whereDate('created_at', '>=', $startDate);
            $responseQuery->whereDate('submitted_at', '>=', $startDate);
        }
        if ($endDate) {
            $surveyQuery->whereDate('created_at', '<=', $endDate);
            $responseQuery->whereDate('submitted_at', '<=', $endDate);
        }

        // --- Stat 1: Total Surveys ---
        $totalSurveys = (clone $surveyQuery)->count();
        $activeSurveys = (clone $surveyQuery)->where('is_active', true)->count();

        // --- Stat 2: Total Responses ---
        $totalResponses = (clone $responseQuery)->count();
        $responsesThisMonthQuery = clone $responseQuery;
        if (!$startDate && !$endDate) {
            $responsesThisMonthQuery->whereMonth('submitted_at', now()->month)
                                    ->whereYear('submitted_at', now()->year);
        }
        $responsesThisMonth = $responsesThisMonthQuery->count();

        // Optimized sparkline data: Single grouped query instead of 7 individual ones
        $sparklineResults = (clone $responseQuery)
            ->select(DB::raw('DATE(submitted_at) as date'), DB::raw('count(*) as aggregate'))
            ->where('submitted_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('date')
            ->pluck('aggregate', 'date')
            ->toArray();

        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $dateString = now()->subDays($i)->format('Y-m-d');
            $chartData[] = $sparklineResults[$dateString] ?? 0;
        }

        // --- Stat 3: Weekly Trend ---
        $trendQueryCurrent = clone $responseQuery;
        $trendQueryPrevious = clone $responseQuery;
        if (!$startDate && !$endDate) {
            $trendQueryCurrent->where('submitted_at', '>=', now()->subDays(7));
            $trendQueryPrevious->whereBetween('submitted_at', [now()->subDays(14), now()->subDays(7)]);
        }
        $last7Days = $trendQueryCurrent->count();
        $previous7Days = $trendQueryPrevious->count();
        $trend = $previous7Days > 0 ? round((($last7Days - $previous7Days) / $previous7Days) * 100) : 0;

        // --- Stat 4: Average Rating ---
        $avgRatingQuery = DB::table('answers')
            ->join('questions', 'answers.question_id', '=', 'questions.id')
            ->join('responses', 'answers.response_id', '=', 'responses.id')
            ->where('questions.type', Question::TYPE_RATING)
            ->whereNotNull('answers.value');
        if ($startDate) {
            $avgRatingQuery->whereDate('responses.submitted_at', '>=', $startDate);
        }
        if ($endDate) {
            $avgRatingQuery->whereDate('responses.submitted_at', '<=', $endDate);
        }
        $avgRating = $avgRatingQuery->avg(DB::raw('CAST(answers.value AS DECIMAL(10,2))'));

        return [
            'totalSurveys' => $totalSurveys,
            'activeSurveys' => $activeSurveys,
            'totalResponses' => $totalResponses,
            'responsesThisMonth' => $responsesThisMonth,
            'responsesChartData' => $chartData,
            'weeklyTrend' => $trend,
            'last7Days' => $last7Days,
            'avgRating' => $avgRating ? number_format($avgRating, 1) : 'N/A',
        ];
    }
}

