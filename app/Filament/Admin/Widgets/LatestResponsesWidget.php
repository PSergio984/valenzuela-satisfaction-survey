<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Response;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestResponsesWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Latest Responses';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Response::query()
                    ->with('survey')
                    ->latest('submitted_at')
                    ->limit(10)
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
