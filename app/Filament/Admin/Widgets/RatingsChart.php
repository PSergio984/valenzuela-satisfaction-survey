<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Question;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class RatingsChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected ?string $heading = 'Rating Distribution';

    protected ?string $description = 'Distribution of all rating responses';

    protected function getData(): array
    {
        // Get rating distribution
        $ratings = DB::table('answers')
            ->join('questions', 'answers.question_id', '=', 'questions.id')
            ->where('questions.type', Question::TYPE_RATING)
            ->whereNotNull('answers.value')
            ->select(DB::raw('answers.value as rating'), DB::raw('COUNT(*) as count'))
            ->groupBy('answers.value')
            ->orderBy('answers.value')
            ->pluck('count', 'rating')
            ->toArray();

        // Ensure all ratings 1-5 are present
        $distribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $distribution[$i] = $ratings[(string) $i] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Responses',
                    'data' => array_values($distribution),
                    'backgroundColor' => [
                        '#ef4444', // 1 star - red
                        '#f97316', // 2 stars - orange
                        '#eab308', // 3 stars - yellow
                        '#84cc16', // 4 stars - lime
                        '#22c55e', // 5 stars - green
                    ],
                    'borderColor' => [
                        '#dc2626',
                        '#ea580c',
                        '#ca8a04',
                        '#65a30d',
                        '#16a34a',
                    ],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => ['1 Star', '2 Stars', '3 Stars', '4 Stars', '5 Stars'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
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
