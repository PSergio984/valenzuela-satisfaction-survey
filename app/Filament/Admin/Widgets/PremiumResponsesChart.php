<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Response;
use Carbon\Carbon;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class PremiumResponsesChart extends ApexChartWidget
{
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
    protected int | string | array $columnSpan = 'full';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
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