<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Response;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class ResponsesChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Responses Over Time';

    protected ?string $description = 'Daily responses for the last 30 days';

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $data = [];
        $labels = [];

        // Get data for last 30 days
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('M d');
            $data[] = Response::whereDate('submitted_at', $date)->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Responses',
                    'data' => $data,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
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
