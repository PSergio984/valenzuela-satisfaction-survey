<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Response;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class PremiumResponsesChart extends ApexChartWidget
{
    use InteractsWithPageFilters;

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
    protected static ?string $heading = 'Response Trends';

    /**
     * Sort
     */
    protected static ?int $sort = 2;

    /**
     * Column Span
     */
    protected int | string | array $columnSpan = ['sm' => 1, 'xl' => 1];

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;

        $query = Response::query();

        if ($startDate) {
            $query->whereDate('submitted_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('submitted_at', '<=', $endDate);
        }

        $data = [];
        $labels = [];

        // Determine date range
        $start = $startDate ? Carbon::parse($startDate) : Carbon::now()->subDays(29);
        $end = $endDate ? Carbon::parse($endDate) : Carbon::now();
        $days = $start->diffInDays($end);
        
        // Cap to 30 days max for performance/display if no filters
        if (!$startDate && !$endDate) {
            $days = 29;
            $start = Carbon::now()->subDays(29);
            $end = Carbon::now();
        }

        // Get data
        for ($i = $days; $i >= 0; $i--) {
            $date = clone $end;
            $date->subDays($i);
            $labels[] = $date->format('M d');
            
            // Build separate query per day to respect other potential filters
            $dayQuery = clone $query;
            $data[] = $dayQuery->whereDate('submitted_at', $date)->count();
        }

        return [
            'chart' => [
                'type' => 'area',
                'height' => 300,
                'toolbar' => [
                    'show' => false,
                ],
                'fontFamily' => 'inherit',
                'animations' => [
                    'enabled' => true,
                    'easing' => 'easeinout',
                    'speed' => 800,
                    'animateGradually' => [
                        'enabled' => true,
                        'delay' => 150
                    ],
                    'dynamicAnimation' => [
                        'enabled' => true,
                        'speed' => 350
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