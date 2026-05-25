<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Response;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestResponsesWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Latest Responses';

    public function table(Table $table): Table
    {
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;

        return $table
            ->query(
                Response::query()
                    ->with('survey')
                    ->when($startDate, fn (Builder $query, $date) => $query->whereDate('submitted_at', '>=', $date))
                    ->when($endDate, fn (Builder $query, $date) => $query->whereDate('submitted_at', '<=', $date))
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
