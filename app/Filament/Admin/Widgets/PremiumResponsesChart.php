<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Response;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\DB;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class PremiumResponsesChart extends ApexChartWidget
{
    use InteractsWithPageFilters;

    /**
     * Disable lazy loading to ensure snappier updates
     */
    protected static bool $isLazy = false;

    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'premiumResponsesChart';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected ?string $heading = 'Response Trends';

    /**
     * Sort
     */
    protected static ?int $sort = 2;

    /**
     * Column Span
     */
    protected int | string | array $columnSpan = ['md' => 8];

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
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

        $days = (int) $start->diffInDays($end);

        // Fetch data using index-friendly range query
        $results = Response::query()
            ->selectRaw('DATE(submitted_at) as date, count(*) as aggregate')
            ->where('submitted_at', '>=', $start)
            ->where('submitted_at', '<=', $end)
            ->groupBy('date')
            ->pluck('aggregate', 'date')
            ->toArray();
        $data = [];
        $labels = [];

        // Single pass to generate labels and data
        for ($i = $days; $i >= 0; $i--) {
            $date = (clone $end)->subDays($i);
            $dateString = $date->format('Y-m-d');

            $labels[] = $date->format('M d');
            $data[] = $results[$dateString] ?? 0;
        }

        return [
            'chart' => [
                'type' => 'area',
                'height' => 300,
                'toolbar' => [
                    'show' => false,
                ],
                'zoom' => [
                    'enabled' => false,
                ],
                'selection' => [
                    'enabled' => false,
                ],
                'animations' => [
                    'enabled' => true,
                    'easing' => 'easeinout',
                    'speed' => 600,
                    'animateGradually' => [
                        'enabled' => true,
                        'delay' => 100
                    ],
                    'dynamicAnimation' => [
                        'enabled' => true,
                        'speed' => 450
                    ]
                ]
            ],
            'series' => [
                [
                    'name' => 'Responses',
                    'data' => $data,
                ],
            ],
            'xaxis' => [
                'categories' => $labels,
                'labels' => [
                    'style' => [
                        'colors' => '#9ca3af',
                        'fontWeight' => 500,
                    ],
                ],
                'axisBorder' => [
                    'show' => false,
                ],
                'axisTicks' => [
                    'show' => false,
                ],
                'tooltip' => [
                    'enabled' => false,
                ]
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'colors' => '#9ca3af',
                        'fontWeight' => 500,
                    ],
                ],
            ],
            'grid' => [
                'show' => true,
                'borderColor' => '#f3f4f6',
                'strokeDashArray' => 4,
                'position' => 'back',
            ],
            'dataLabels' => [
                'enabled' => false,
            ],
            'stroke' => [
                'curve' => 'smooth',
                'width' => 3,
                'colors' => ['#2563EB'], // Primary Brand
            ],
            'fill' => [
                'type' => 'gradient',
                'gradient' => [
                    'shadeIntensity' => 1,
                    'opacityFrom' => 0.45,
                    'opacityTo' => 0.05,
                    'stops' => [0, 100],
                    'colorStops' => [
                        [
                            'offset' => 0,
                            'color' => '#3B82F6', // Secondary Brand
                            'opacity' => 0.4
                        ],
                        [
                            'offset' => 100,
                            'color' => '#3B82F6',
                            'opacity' => 0.0
                        ]
                    ]
                ],
            ],
            'colors' => ['#2563EB'],
            'tooltip' => [
                'theme' => 'light',
                'y' => [
                    'formatter' => 'function (val) { return val + " responses" }'
                ]
            ]
        ];
    }
}