<?php

namespace App\Observers;

use App\Models\Answer;
use App\Models\Question;
use Filament\Notifications\Notification;

class AnswerObserver
{
    /**
     * Handle the Answer "created" event.
     */
    public function created(Answer $answer): void
    {
        // Skip alerts during seeding
        if (config('app.seeding')) {
            return;
        }

        $question = $answer->question;

        if ($question && $question->type === Question::TYPE_RATING) {
            $ratingValue = (float) $answer->value;

            // Trigger alert for "Detractors" (ratings < 3)
            if ($ratingValue > 0 && $ratingValue < 3) {
                $response = $answer->response;
                $survey = $response?->survey;
                $owner = $survey?->creator; 

                if (! $owner) {
                    return;
                }

                Notification::make()
                    ->title('Low Rating Alert')
                    ->body("Survey '{$survey->title}' received a low rating ({$ratingValue}/5) from " . ($response->respondent_name ?? 'Anonymous') . ".")
                    ->warning()
                    ->actions([
                        \Filament\Actions\Action::make('view')
                            ->button()
                            ->url(route('filament.admin.resources.responses.view', ['record' => $response->id])),
                    ])
                    ->sendToDatabase($owner);
            }
        }
    }
}
