<?php

namespace App\Filament\Admin\Resources\Surveys\Schemas;

use App\Enums\SurveyMode;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SurveyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Survey Details')
                    ->description('Basic information about the survey')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),

                        Radio::make('mode')
                            ->label('Survey Mode')
                            ->options(SurveyMode::class)
                            ->default(SurveyMode::Simple)
                            ->inline()
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('slug')
                            ->helperText('Leave empty to auto-generate from title')
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        TextInput::make('thank_you_message')
                            ->label('Thank You Message')
                            ->placeholder('Thank you for your feedback!')
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Section::make('Settings')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Active')
                            ->helperText('Only active surveys can receive responses')
                            ->default(false),

                        Toggle::make('is_public')
                            ->label('Public')
                            ->helperText('Public surveys are listed in the directory')
                            ->default(false),

                        Toggle::make('requires_authentication')
                            ->label('Require Authentication')
                            ->helperText('Require users to log in before responding'),

                        Toggle::make('collect_respondent_info')
                            ->label('Collect Respondent Info')
                            ->helperText('Ask for name, email, and phone')
                            ->default(true),
                    ])
                    ->columns(4),

                Section::make('Schedule')
                    ->description('Optional start and end dates for the survey')
                    ->schema([
                        DateTimePicker::make('starts_at')
                            ->label('Start Date'),

                        DateTimePicker::make('ends_at')
                            ->label('End Date')
                            ->afterOrEqual('starts_at'),
                    ])
                    ->columns(2),
            ]);
    }
}
