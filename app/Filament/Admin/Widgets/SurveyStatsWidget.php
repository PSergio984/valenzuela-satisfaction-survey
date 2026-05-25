<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Question;
use App\Models\Response;
use App\Models\Survey;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class SurveyStatsWidget extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;

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

        // Get total surveys
        $totalSurveys = $surveyQuery->count();
        $activeSurveys = (clone $surveyQuery)->where('is_active', true)->count();

        // Get total responses
        $totalResponses = $responseQuery->count();
        
        $responsesThisMonthQuery = clone $responseQuery;
        if (!$startDate && !$endDate) {
            $responsesThisMonthQuery->whereMonth('submitted_at', now()->month)
                                    ->whereYear('submitted_at', now()->year);
        }
        $responsesThisMonth = $responsesThisMonthQuery->count();

        // Get response trend (last 7 days vs previous 7 days)
        $trendQueryCurrent = clone $responseQuery;
        $trendQueryPrevious = clone $responseQuery;
        
        if (!$startDate && !$endDate) {
            $trendQueryCurrent->where('submitted_at', '>=', now()->subDays(7));
            $trendQueryPrevious->whereBetween('submitted_at', [now()->subDays(14), now()->subDays(7)]);
        }
        
        $last7Days = $trendQueryCurrent->count();
        $previous7Days = $trendQueryPrevious->count();
        $trend = $previous7Days > 0 ? round((($last7Days - $previous7Days) / $previous7Days) * 100) : 0;

        // Get average rating across all rating questions
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

        // Generate chart data for last 7 days
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $chartDataQuery = clone $responseQuery;
            $chartData[] = $chartDataQuery->whereDate('submitted_at', $date)->count();
        }

        return [
            Stat::make('Total Surveys', $totalSurveys)
                ->description("{$activeSurveys} active")
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('primary'),

            Stat::make('Total Responses', $totalResponses)
                ->description("{$responsesThisMonth} this month")
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->chart($chartData)
                ->color('success'),

            Stat::make('Weekly Trend', ($trend >= 0 ? '+' : '').$trend.'%')
                ->description("{$last7Days} responses this week")
                ->descriptionIcon($trend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($trend >= 0 ? 'success' : 'danger'),

            Stat::make('Average Rating', $avgRating ? number_format($avgRating, 1).'/5' : 'N/A')
                ->description('Across all surveys')
                ->descriptionIcon('heroicon-m-star')
                ->color('warning'),
        ];
    }
}
