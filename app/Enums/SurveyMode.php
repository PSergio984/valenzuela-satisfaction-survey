<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum SurveyMode: string implements HasColor, HasIcon, HasLabel
{
    case Simple = 'simple';
    case Stepped = 'stepped';
    case Conversational = 'conversational';

    public function getLabel(): string
    {
        return match ($this) {
            self::Simple => 'Simple (Single Page)',
            self::Stepped => 'Stepped (Multi-Step)',
            self::Conversational => 'Conversational',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Simple => 'info',
            self::Stepped => 'warning',
            self::Conversational => 'success',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Simple => 'heroicon-o-document-text',
            self::Stepped => 'heroicon-o-queue-list',
            self::Conversational => 'heroicon-o-chat-bubble-left-right',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::Simple => 'All questions on a single page',
            self::Stepped => 'Questions split into multiple steps/pages',
            self::Conversational => 'One question at a time with smooth transitions',
        };
    }
}
