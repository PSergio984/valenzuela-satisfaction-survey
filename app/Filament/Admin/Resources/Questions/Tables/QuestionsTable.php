<?php

namespace App\Filament\Admin\Resources\Questions\Tables;

use App\Models\Question;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class QuestionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('survey.title')
                    ->label('Survey')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                TextColumn::make('order')
                    ->label('#')
                    ->sortable(),

                TextColumn::make('question')
                    ->searchable()
                    ->limit(50),

                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => Question::TYPES[$state] ?? $state),

                IconColumn::make('is_required')
                    ->label('Required')
                    ->boolean(),

                TextColumn::make('options_count')
                    ->label('Options')
                    ->counts('options'),
            ])
            ->defaultSort('order')
            ->filters([
                SelectFilter::make('survey')
                    ->relationship('survey', 'title'),

                SelectFilter::make('type')
                    ->options(Question::TYPES),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
