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
                            ->required()
                            ->live(onBlur: true)
                            ->minValue(0)
                            ->step(1)
                            ->validationAttribute('order')
                            ->helperText('Must be 0 or greater')
                            ->rules([
                                'required',
                                'integer',
                                'min:0',
                                function () {
                                    return function (string $attribute, $value, \Closure $fail) {
                                        if ($value < 0) {
                                            $fail('Please enter a positive value (0 or greater).');
                                        }
                                    };
                                },
                            ])
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state !== null && $state < 0) {
                                    throw ValidationException::withMessages([
                                        'data.order' => 'Please enter a positive value (0 or greater).',
                                    ]);
                                }
                            }),

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
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(1)
                            ->validationAttribute('minimum value')
                            ->helperText('Must be 0 or greater and less than maximum value')
                            ->rules([
                                'required',
                                'integer',
                                'min:0',
                                function (Get $get) {
                                    return function (string $attribute, $value, \Closure $fail) use ($get) {
                                        if ($value < 0) {
                                            $fail('Please enter a positive value (0 or greater).');
                                        }

                                        $max = $get('settings.max');
                                        if ($max !== null && $value >= $max) {
                                            $fail('Please make the minimum value less than the maximum value (' . $max . ').');
                                        }
                                    };
                                },
                            ])
                            ->afterStateUpdated(function ($state, $get, $set) {
                                if ($state !== null && (int) $state < 0) {
                                    throw ValidationException::withMessages([
                                        'data.settings.min' => 'Please enter a positive value (0 or greater).',
                                    ]);
                                }

                                $max = $get('settings.max');
                                if ($max !== null && $state !== null && (int) $state >= (int) $max) {
                                    throw ValidationException::withMessages([
                                        'data.settings.min' => 'Please make the minimum value less than the maximum value (' . $max . ').',
                                    ]);
                                }
                            }),

                        TextInput::make('settings.max')
                            ->label('Maximum Value')
                            ->numeric()
                            ->default(5)
                            ->required()
                            ->live(onBlur: true)
                            ->minValue(1)
                            ->maxValue(100)
                            ->step(1)
                            ->validationAttribute('maximum value')
                            ->helperText('Must be at least 1 and greater than minimum value')
                            ->rules([
                                'required',
                                'integer',
                                'min:1',
                                function (Get $get) {
                                    return function (string $attribute, $value, \Closure $fail) use ($get) {
                                        if ($value < 1) {
                                            $fail('Please enter a value of at least 1.');
                                        }

                                        $min = $get('settings.min');
                                        if ($min !== null && $min >= $value) {
                                            $fail('Please make the maximum value greater than the minimum value (' . $min . ').');
                                        }
                                    };
                                },
                            ])
                            ->afterStateUpdated(function ($state, $get, $set) {
                                if ($state !== null && (int) $state < 1) {
                                    throw ValidationException::withMessages([
                                        'data.settings.max' => 'Please enter a value of at least 1.',
                                    ]);
                                }

                                $min = $get('settings.min');
                                if ($min !== null && $state !== null && (int) $min >= (int) $state) {
                                    throw ValidationException::withMessages([
                                        'data.settings.max' => 'Please make the maximum value greater than the minimum value (' . $min . ').',
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
