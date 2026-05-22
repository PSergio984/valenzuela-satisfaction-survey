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
            $this->getSaveFormAction(),
            $this->getCancelFormAction(),
        ];
    }

    protected function beforeSave(): void
    {
        // Validate order field
        $order = $this->data['order'] ?? null;
        if ($order !== null) {
            if (is_string($order)) {
                $order = (int) $order;
            }

            if ($order < 0) {
                Notification::make()
                    ->danger()
                    ->title('Invalid Order Value')
                    ->body('Order cannot be negative. Please enter a value of 0 or greater.')
                    ->persistent()
                    ->send();

                $this->halt();
            }
        }

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

            // Check if min is greater than or equal to max
            if ($min !== null && $max !== null && $min >= $max) {
                Notification::make()
                    ->danger()
                    ->title('Invalid Rating Settings')
                    ->body('Maximum value must be greater than minimum value (they cannot be equal).')
                    ->persistent()
                    ->send();

                $this->halt();
            }
        }
    }
}
