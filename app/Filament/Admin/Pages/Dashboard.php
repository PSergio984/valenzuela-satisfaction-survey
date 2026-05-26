<?php

namespace App\Filament\Admin\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Grid::make([
                    'default' => 1,
                    'sm' => 3,
                ])
                ->schema([
                    Select::make('range')
                        ->label('Time Range')
                        ->options([
                            'today' => 'Last 24 Hours',
                            '7_days' => 'Last 7 Days',
                            '30_days' => 'Last 30 Days',
                            'custom' => 'Custom Range',
                        ])
                        ->default('7_days')
                        ->live(debounce: 0)
                        ->afterStateUpdated(function (?string $state, Set $set) {
                            if (! $state || $state === 'custom') {
                                return;
                            }

                            $set('startDate', match ($state) {
                                'today' => now()->startOfDay()->format('Y-m-d'),
                                '7_days' => now()->subDays(6)->startOfDay()->format('Y-m-d'),
                                '30_days' => now()->subDays(29)->startOfDay()->format('Y-m-d'),
                                default => null,
                            });

                            $set('endDate', now()->format('Y-m-d'));
                        })
                        ->native(false),
                    DatePicker::make('startDate')
                        ->label('From')
                        ->maxDate(fn (Get $get) => $get('endDate') ?: now())
                        ->visible(fn (Get $get) => $get('range') === 'custom')
                        ->live(onBlur: true)
                        ->native(false),
                    DatePicker::make('endDate')
                        ->label('To')
                        ->minDate(fn (Get $get) => $get('startDate'))
                        ->maxDate(now())
                        ->visible(fn (Get $get) => $get('range') === 'custom')
                        ->live(onBlur: true)
                        ->native(false),
                ])
                ->columnSpan('full'),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getFiltersFormContentComponent(),
                Grid::make([
                    'md' => 3,
                ])
                ->schema([
                    ...$this->getWidgetsSchemaComponents([
                        \App\Filament\Admin\Widgets\SurveyStatsWidget::class,
                    ]),
                    ...$this->getWidgetsSchemaComponents([
                        \App\Filament\Admin\Widgets\PremiumResponsesChart::class,
                        \App\Filament\Admin\Widgets\RatingsChart::class,
                        \App\Filament\Admin\Widgets\LatestResponsesWidget::class,
                    ]),
                ]),
            ]);
    }
}