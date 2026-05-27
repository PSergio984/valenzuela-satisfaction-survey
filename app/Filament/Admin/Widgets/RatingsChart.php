<?php

namespace App\Filament\Admin\Widgets;

use Carbon\Carbon;
use App\Models\Question;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\DB;

class RatingsChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static bool $isLazy = false;

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = ['md' => 1];

    protected ?string $heading = 'Rating Distribution';

    protected ?string $description = 'Distribution of all rating responses';

    protected function getData(): array
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

        // Get rating distribution
        $query = DB::table('answers')
            ->join('questions', 'answers.question_id', '=', 'questions.id')
            ->join('responses', 'answers.response_id', '=', 'responses.id')
            ->where('questions.type', Question::TYPE_RATING)
            ->whereNotNull('answers.value')
            ->where('responses.submitted_at', '>=', $start)
            ->where('responses.submitted_at', '<=', $end);

        $ratings = $query->select(DB::raw('answers.value as rating'), DB::raw('COUNT(*) as count'))
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
            'animation' => [
                'duration' => 1000,
                'easing' => 'easeOutQuart',
            ],
            'transitions' => [
                'active' => [
                    'animation' => [
                        'duration' => 400
                    ]
                ]
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
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
