<?php

namespace App\Filament\Admin\Resources\Surveys\RelationManagers;

use App\Models\Question;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class QuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'questions';

    protected static ?string $recordTitleAttribute = 'question';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Question Details')
                    ->schema([
                        Select::make('type')
                            ->options(Question::TYPES)
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn ($state, callable $set) => $set('options', [])),

                        TextInput::make('order')
                            ->numeric()
                            ->default(0)
                            ->required(),

                        Textarea::make('question')
                            ->label('Question Text')
                            ->required()
                            ->rows(2)
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->label('Helper Text')
                            ->rows(2)
                            ->columnSpanFull(),

                        Toggle::make('is_required')
                            ->label('Required')
                            ->default(false),
                    ])
                    ->columns(2),

                Section::make('Options')
                    ->description('Add options for choice-based questions')
                    ->schema([
                        Repeater::make('options')
                            ->relationship()
                            ->schema([
                                TextInput::make('label')
                                    ->required()
                                    ->columnSpan(2),

                                TextInput::make('value')
                                    ->placeholder('Same as label if empty'),

                                TextInput::make('order')
                                    ->numeric()
                                    ->default(0),
                            ])
                            ->columns(4)
                            ->orderColumn('order')
                            ->reorderable()
                            ->addActionLabel('Add Option')
                            ->defaultItems(0),
                    ])
                    ->visible(fn (Get $get): bool => in_array($get('type'), [
                        Question::TYPE_RADIO,
                        Question::TYPE_CHECKBOX,
                        Question::TYPE_SELECT,
                    ])),

                Section::make('Rating Settings')
                    ->schema([
                        TextInput::make('settings.min')
                            ->label('Minimum Value')
                            ->numeric()
                            ->default(1),

                        TextInput::make('settings.max')
                            ->label('Maximum Value')
                            ->numeric()
                            ->default(5),

                        TextInput::make('settings.min_label')
                            ->label('Min Label')
                            ->placeholder('e.g., Poor'),

                        TextInput::make('settings.max_label')
                            ->label('Max Label')
                            ->placeholder('e.g., Excellent'),
                    ])
                    ->columns(4)
                    ->visible(fn (Get $get): bool => $get('type') === Question::TYPE_RATING),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order')
                    ->label('#')
                    ->sortable(),

                TextColumn::make('question')
                    ->limit(50)
                    ->searchable(),

                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Question::TYPES[$state] ?? $state),

                IconColumn::make('is_required')
                    ->label('Required')
                    ->boolean(),

                TextColumn::make('options_count')
                    ->label('Options')
                    ->counts('options'),
            ])
            ->defaultSort('order')
            ->reorderable('order')
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }
}
