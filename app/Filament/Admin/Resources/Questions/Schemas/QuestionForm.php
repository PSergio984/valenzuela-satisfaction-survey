<?php

namespace App\Filament\Admin\Resources\Questions\Schemas;

use App\Models\Question;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Validation\ValidationException;

class QuestionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Question Details')
                    ->schema([
                        Select::make('survey_id')
                            ->relationship('survey', 'title')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Select::make('type')
                            ->options(Question::TYPES)
                            ->required()
                            ->live(),

                        TextInput::make('order')
                            ->numeric()
                            ->default(0)
                            ->required(),

                        Toggle::make('is_required')
                            ->label('Required')
                            ->default(false),

                        Textarea::make('question')
                            ->label('Question Text')
                            ->required()
                            ->rows(2)
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->label('Helper Text')
                            ->rows(2)
                            ->columnSpanFull(),
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
                            ->default(1)
                            ->required()
                            ->live(onBlur: true)
                            ->rules([
                                'required',
                                'integer',
                                'min:0',
                                function () {
                                    return function (string $attribute, $value, $fail) {
                                        if ($value < 0) {
                                            $fail('Minimum value cannot be negative. Please enter 0 or greater.');
                                        }
                                    };
                                },
                            ]),

                        TextInput::make('settings.max')
                            ->label('Maximum Value')
                            ->numeric()
                            ->default(5)
                            ->required()
                            ->live(onBlur: true)
                            ->rules([
                                'required',
                                'integer',
                                'min:1',
                                function () {
                                    return function (string $attribute, $value, $fail) {
                                        if ($value < 1) {
                                            $fail('Maximum value must be at least 1.');
                                        }
                                    };
                                },
                            ])
                            ->afterStateUpdated(function ($state, $get, $fail) {
                                $min = $get('settings.min');
                                if ($min !== null && $state !== null && (int) $min > (int) $state) {
                                    throw ValidationException::withMessages([
                                        'data.settings.max' => 'Maximum value must be greater than minimum value.',
                                    ]);
                                }
                            }),

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
}
