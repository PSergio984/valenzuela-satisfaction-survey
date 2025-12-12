<?php

namespace App\Filament\Admin\Resources\Responses\Pages;

use App\Filament\Admin\Resources\Responses\ResponseResource;
use App\Models\Question;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Placeholder;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewResponse extends ViewRecord
{
    protected static string $resource = ResponseResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Survey Information')
                    ->schema([
                        TextEntry::make('survey.title')
                            ->label('Survey'),

                        TextEntry::make('submitted_at')
                            ->label('Submitted At')
                            ->dateTime('M d, Y h:i A')
                            ->timezone('Asia/Manila'),
                    ])
                    ->columns([
                        'sm' => 1,
                        'md' => 2,
                    ])
                    ->compact(),

                Section::make('Respondent Information')
                    ->schema([
                        TextEntry::make('respondent_name')
                            ->label('Name')
                            ->default('Anonymous'),

                        TextEntry::make('respondent_email')
                            ->label('Email')
                            ->default('-'),

                        TextEntry::make('respondent_phone')
                            ->label('Phone')
                            ->default('-'),

                        // IP Address removed
                    ])
                    ->columns([
                        'sm' => 1,
                        'md' => 2,
                        'lg' => 3,
                    ])
                    ->compact(),

                // Section::make('Answers')
                //     ->schema(function ($record) {
                //         $entries = [];

                //         // Load answers with question and options relationships
                //         $answers = $record->answers()
                //             ->with(['question', 'question.options'])
                //             ->orderBy('question_id')
                //             ->get();

                //         foreach ($answers as $answer) {
                //             $question = $answer->question;

                //             if (!$question) {
                //                 continue; // Skip if question was deleted
                //             }

                //             $value = $this->formatAnswerValue($answer, $question);

                //             $entries[] = Placeholder::make('answer_' . $answer->id)
                //                 ->label($question->question)
                //                 ->content($value)
                //                 ->columnSpanFull();
                //         }

                //         if (empty($entries)) {
                //             $entries[] = Placeholder::make('no_answers')
                //                 ->label('No Answers')
                //                 ->content('This response has no answers recorded.')
                //                 ->columnSpanFull();
                //         }

                //         return $entries;
                //     }),
            ]);
    }

    protected function formatAnswerValue($answer, Question $question): string
    {
        // Handle rating type
        if ($question->type === Question::TYPE_RATING) {
            $value = $answer->value ?? '';
            return $value ? $value . ' / 5' : '-';
        }

        // For radio, checkbox, and select - check selected_options first
        if (in_array($question->type, [Question::TYPE_CHECKBOX, Question::TYPE_RADIO, Question::TYPE_SELECT])) {
            // If selected_options array has IDs, convert to labels
            if (!empty($answer->selected_options) && is_array($answer->selected_options)) {
                $options = $question->options->whereIn('id', $answer->selected_options);
                if ($options->isNotEmpty()) {
                    return $options->pluck('label')->join(', ');
                }
            }

            // Fall back to value field (which might contain the option value directly)
            if ($answer->value) {
                // Try to find matching option by value
                $option = $question->options->where('value', $answer->value)->first();
                if ($option) {
                    return $option->label;
                }
                return $answer->value; // Return raw value if no match
            }
        }

        // For text, textarea, and other types - return the value field
        return $answer->value ?? '-';
    }
}
