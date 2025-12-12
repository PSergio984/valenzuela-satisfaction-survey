<?php

namespace App\Filament\Admin\Resources\Responses\Pages;

use App\Filament\Admin\Resources\Responses\ResponseResource;
use App\Models\Question;
use Filament\Infolists\Components\TextEntry;
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
                            ->dateTime('M d, Y H:i:s'),
                    ])
                    ->columns(2),

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
                    ->columns(2),

                Section::make('Answers')
                    ->schema(function ($record) {
                        $entries = [];

                        foreach ($record->answers()->with('question.options')->get() as $answer) {
                            $question = $answer->question;
                            $value = $this->formatAnswerValue($answer, $question);

                            $entries[] = TextEntry::make('answer_' . $answer->id)
                                ->label($question->question)
                                ->state($value)
                                ->columnSpanFull();
                        }

                        return $entries;
                    }),
            ]);
    }

    protected function formatAnswerValue($answer, Question $question): string
    {
        if (in_array($question->type, [Question::TYPE_CHECKBOX, Question::TYPE_RADIO, Question::TYPE_SELECT])) {
            if (! empty($answer->selected_options)) {
                $options = $question->options->whereIn('id', $answer->selected_options);

                return $options->pluck('label')->join(', ');
            }
        }

        return $answer->value ?? '-';
    }
}
