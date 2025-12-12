<?php

namespace App\Filament\Admin\Resources\Questions\Pages;

use App\Filament\Admin\Resources\Questions\QuestionResource;
use App\Models\Question;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditQuestion extends EditRecord
{
    protected static string $resource = QuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->disabled(function () {
                    // Disable save button if rating type has negative values
                    if (isset($this->data['type']) && $this->data['type'] === Question::TYPE_RATING) {
                        $min = $this->data['settings']['min'] ?? null;
                        $max = $this->data['settings']['max'] ?? null;

                        if ($min !== null && (int) $min < 0) {
                            return true;
                        }
                        if ($max !== null && (int) $max < 1) {
                            return true;
                        }
                    }
                    return false;
                }),
            $this->getCancelFormAction(),
        ];
    }

    protected function beforeSave(): void
    {
        // Validate rating settings if type is rating
        if ($this->data['type'] === Question::TYPE_RATING) {
            $min = $this->data['settings']['min'] ?? null;
            $max = $this->data['settings']['max'] ?? null;

            // Convert to numeric if they're strings
            if (is_string($min)) {
                $min = (int) $min;
            }
            if (is_string($max)) {
                $max = (int) $max;
            }

            // Check for negative minimum value
            if ($min !== null && $min < 0) {
                Notification::make()
                    ->danger()
                    ->title('Invalid Rating Settings')
                    ->body('Minimum value cannot be negative. Please enter a value of 0 or greater.')
                    ->persistent()
                    ->send();

                $this->halt();
            }

            // Check for invalid maximum value
            if ($max !== null && $max < 1) {
                Notification::make()
                    ->danger()
                    ->title('Invalid Rating Settings')
                    ->body('Maximum value must be at least 1.')
                    ->persistent()
                    ->send();

                $this->halt();
            }

            // Check if min is greater than max
            if ($min !== null && $max !== null && $min > $max) {
                Notification::make()
                    ->danger()
                    ->title('Invalid Rating Settings')
                    ->body('Minimum value cannot be greater than maximum value.')
                    ->persistent()
                    ->send();

                $this->halt();
            }
        }
    }
}
