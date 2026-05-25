<?php

namespace App\Filament\Admin\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Filter by Date')
                    ->description('Limit dashboard data to a specific date range.')
                    ->schema([
                        DatePicker::make('startDate')
                            ->label('Start Date')
                            ->maxDate(fn (string $operation) => now()),
                        DatePicker::make('endDate')
                            ->label('End Date')
                            ->maxDate(fn (string $operation) => now()),
                    ])
                    ->columns(2),
            ]);
    }
}