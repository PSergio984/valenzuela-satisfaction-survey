<?php

namespace App\Filament\Admin\Resources\Surveys\Widgets;

use App\Models\Survey;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Model;

class SurveyResponsesChart extends ChartWidget
{
    public ?Model $record = null;

    protected ?string $heading = 'Responses Over Time';

    protected ?string $description = 'Daily responses for this survey (last 30 days)';

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        /** @var Survey $survey */
        $survey = $this->record;

        if (! $survey) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $data = [];
        $labels = [];

        // Get data for last 30 days
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('M d');
            $data[] = $survey->responses()
                ->whereDate('submitted_at', $date)
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Responses',
                    'data' => $data,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
        ];
    }
}
