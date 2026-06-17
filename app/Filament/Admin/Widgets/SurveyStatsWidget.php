<?php

namespace App\Filament\Admin\Widgets;

use Carbon\Carbon;
use App\Models\Question;
use App\Models\Response;
use App\Models\Survey;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\Widget as BaseWidget;
use Illuminate\Support\Facades\DB;

class SurveyStatsWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static bool $isLazy = false;

    protected string $view = 'filament.admin.widgets.survey-stats-widget';

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 12;

    protected function getViewData(): array
    {
        $range = $this->filters['range'] ?? '7_days';
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;

        // Determine date range
        $end = now();
        $start = now()->subDays(29);

        if ($range === 'custom') {
            $end = $endDate ? Carbon::parse($endDate)->endOfDay() : now();
            $start = $startDate ? Carbon::parse($startDate)->startOfDay() : now()->subDays(29)->startOfDay();
        } else {
            $start = match ($range) {
                'today' => now()->startOfDay(),
                '7_days' => now()->subDays(6)->startOfDay(),
                '30_days' => now()->subDays(29)->startOfDay(),
                default => now()->subDays(29)->startOfDay(),
            };
        }

        // --- Stat 1: Total Surveys ---
        $surveys = Survey::query()
            ->where('created_at', '>=', $start)
            ->where('created_at', '<=', $end)
            ->selectRaw('count(*) as total, sum(case when is_active = true then 1 else 0 end) as active')
            ->first();

        $totalSurveys = $surveys->total ?? 0;
        $activeSurveys = $surveys->active ?? 0;

        // --- Stat 2: Total Responses ---
        $responseQuery = Response::query()
            ->where('submitted_at', '>=', $start)
            ->where('submitted_at', '<=', $end);

        $totalResponses = (clone $responseQuery)->count();
        
        $responsesThisMonth = (clone $responseQuery)
            ->whereMonth('submitted_at', now()->month)
            ->whereYear('submitted_at', now()->year)
            ->count();

        // Optimized sparkline data
        $sparklineResults = (clone $responseQuery)
            ->selectRaw('DATE(submitted_at) as date, count(*) as aggregate')
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

