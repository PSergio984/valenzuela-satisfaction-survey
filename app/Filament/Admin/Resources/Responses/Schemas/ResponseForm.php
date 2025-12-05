<?php

namespace App\Filament\Admin\Resources\Responses\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ResponseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Survey Information')
                    ->schema([
                        Placeholder::make('survey')
                            ->label('Survey')
                            ->content(fn ($record) => $record?->survey?->title),

                        Placeholder::make('submitted_at')
                            ->label('Submitted At')
                            ->content(fn ($record) => $record?->submitted_at?->format('M d, Y H:i:s')),
                    ])
                    ->columns(2),

                Section::make('Respondent Information')
                    ->schema([
                        Placeholder::make('respondent_name')
                            ->label('Name')
                            ->content(fn ($record) => $record?->respondent_name ?? 'Anonymous'),

                        Placeholder::make('respondent_email')
                            ->label('Email')
                            ->content(fn ($record) => $record?->respondent_email ?? '-'),

                        Placeholder::make('respondent_phone')
                            ->label('Phone')
                            ->content(fn ($record) => $record?->respondent_phone ?? '-'),

                        Placeholder::make('ip_address')
                            ->label('IP Address')
                            ->content(fn ($record) => $record?->ip_address ?? '-'),
                    ])
                    ->columns(2),
            ]);
    }
}
