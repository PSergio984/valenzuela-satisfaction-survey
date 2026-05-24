<?php

namespace App\Jobs;

use App\Models\Question;
use App\Models\Survey;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class GenerateSurveyPdfReport implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected int $surveyId,
        protected int $userId,
        protected string $filename
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $survey = Survey::findOrFail($this->surveyId);
        $user = User::findOrFail($this->userId);

        $survey->load(['questions' => fn ($q) => $q->orderBy('order'), 'responses.answers']);

        // Calculate statistics for each question
        $statistics = $this->calculateStatistics($survey);

        $pdf = Pdf::loadView('exports.survey-responses', [
            'survey' => $survey,
            'statistics' => $statistics,
            'generatedAt' => now(),
        ]);

        // Store PDF on private disk
        Storage::disk('private')->put($this->filename, $pdf->output());

        // Send silent notification
        Notification::make()
            ->title('PDF Report Ready')
            ->body("The report for survey '{$survey->title}' is ready for download.")
            ->actions([
                \Filament\Actions\Action::make('download')
                    ->button()
                    ->url(route('surveys.exports.download', ['path' => $this->filename])),
            ])
            ->sendToDatabase($user);
    }

    /**
     * Calculate statistics for a survey.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function calculateStatistics(Survey $survey): array
    {
        $statistics = [];

        foreach ($survey->questions as $question) {
            $questionStats = [
                'question' => $question->question,
                'type' => $question->type,
                'total_responses' => 0,
                'answers' => [],
            ];

            $allAnswers = $survey->responses->flatMap(function ($response) use ($question) {
                return $response->answers->where('question_id', $question->id);
            });

            $questionStats['total_responses'] = $allAnswers->count();

            switch ($question->type) {
                case Question::TYPE_RATING:
                    $values = $allAnswers->pluck('value')->filter()->map(fn ($v) => (float) $v);
                    $questionStats['average'] = $values->count() > 0 ? round($values->avg(), 2) : 0;
                    $questionStats['distribution'] = $values->countBy()->sortKeys()->all();
                    break;

                case Question::TYPE_RADIO:
                case Question::TYPE_SELECT:
                    $questionStats['distribution'] = $allAnswers->pluck('value')
                        ->filter()
                        ->countBy()
                        ->sortDesc()
                        ->all();
                    break;

                case Question::TYPE_CHECKBOX:
                    $allOptions = $allAnswers->flatMap(fn ($a) => $a->selected_options ?? []);
                    $questionStats['distribution'] = $allOptions->countBy()->sortDesc()->all();
                    break;

                case Question::TYPE_TEXT:
                case Question::TYPE_TEXTAREA:
                    $questionStats['sample_answers'] = $allAnswers->pluck('value')
                        ->filter()
                        ->take(5)
                        ->values()
                        ->all();
                    break;
            }

            $statistics[$question->id] = $questionStats;
        }

        return $statistics;
    }
}
