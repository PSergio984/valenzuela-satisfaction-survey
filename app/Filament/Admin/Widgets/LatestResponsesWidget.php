<?php

namespace App\Filament\Admin\Widgets;

use Carbon\Carbon;
use App\Models\Response;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestResponsesWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static bool $isLazy = false;

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 12;

    protected static ?string $heading = 'Latest Responses';

    public function table(Table $table): Table
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

        return $table
            ->query(
                Response::query()
                    ->with('survey')
                    ->where('submitted_at', '>=', $start)
                    ->where('submitted_at', '<=', $end)
                    ->latest('submitted_at')
            )
            ->columns([
                TextColumn::make('survey.title')
                    ->label('Survey')
                    ->limit(30)
                    ->searchable(),

                TextColumn::make('respondent_name')
                    ->label('Respondent')
                    ->default('Anonymous')
                    ->searchable(),

                TextColumn::make('respondent_email')
                    ->label('Email')
                    ->default('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                // IP column removed

                TextColumn::make('submitted_at')
                    ->label('Submitted')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('submitted_at', 'desc')
            ->paginated([5, 10, 25]);
    }
}
