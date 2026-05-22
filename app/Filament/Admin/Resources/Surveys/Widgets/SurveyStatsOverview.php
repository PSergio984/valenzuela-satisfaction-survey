<?php

namespace App\Filament\Admin\Resources\Surveys\Widgets;

use App\Models\Survey;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;

class SurveyStatsOverview extends StatsOverviewWidget
{
    public ?Model $record = null;

    protected function getStats(): array
    {
        /** @var Survey $survey */
        $survey = $this->record;

        if (! $survey) {
            return [];
        }

        $totalResponses = $survey->responses()->count();
        $completedResponses = $survey->responses()->whereNotNull('submitted_at')->count();
        $questionsCount = $survey->questions()->count();

        return [
            Stat::make('Views', number_format($survey->views_count ?? 0))
                ->description('Total survey views')
                ->descriptionIcon('heroicon-m-eye')
                ->color('gray'),

            Stat::make('Starts', number_format($survey->starts_count ?? 0))
                ->description('Survey starts')
                ->descriptionIcon('heroicon-m-play')
                ->color('info'),

            Stat::make('Responses', number_format($completedResponses))
                ->description('Completed submissions')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Completion Rate', $survey->completion_rate.'%')
                ->description('Of all starts')
                ->descriptionIcon($survey->completion_rate >= 50 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($survey->completion_rate >= 50 ? 'success' : 'warning'),

            Stat::make('Avg. Time', $survey->formatted_average_completion_time)
                ->description('To complete survey')
                ->descriptionIcon('heroicon-m-clock')
                ->color('gray'),

            Stat::make('Questions', number_format($questionsCount))
                ->description('Total questions')
                ->descriptionIcon('heroicon-m-question-mark-circle')
                ->color('primary'),
        ];
    }
}
