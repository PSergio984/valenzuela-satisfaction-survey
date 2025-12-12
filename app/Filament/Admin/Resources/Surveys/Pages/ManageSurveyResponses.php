<?php

namespace App\Filament\Admin\Resources\Surveys\Pages;

use App\Filament\Admin\Resources\Surveys\SurveyResource;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ManageSurveyResponses extends ManageRelatedRecords
{
    protected static string $resource = SurveyResource::class;

    protected static string $relationship = 'responses';

    protected static ?string $navigationLabel = 'Submissions';

    protected static ?string $title = 'Submissions';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-inbox-stack';

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Respondent Information')
                    ->schema([
                        TextEntry::make('respondent_name')
                            ->label('Name')
                            ->placeholder('Anonymous'),

                        TextEntry::make('respondent_email')
                            ->label('Email')
                            ->placeholder('Not provided'),

                        TextEntry::make('respondent_phone')
                            ->label('Phone')
                            ->placeholder('Not provided'),

                        TextEntry::make('submitted_at')
                            ->label('Submitted')
                            ->dateTime('M d, Y \a\t g:i A'),

                        TextEntry::make('formatted_time_to_complete')
                            ->label('Time to Complete'),

                        // IP Address removed
                    ])
                    ->columns(3),

                Section::make('Answers')
                    ->schema([
                        RepeatableEntry::make('answers')
                            ->schema([
                                TextEntry::make('question.question')
                                    ->label('Question')
                                    ->columnSpanFull(),

                                TextEntry::make('answer')
                                    ->label('Answer')
                                    ->columnSpanFull(),
                            ])
                            ->columns(1),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('respondent_name')
                    ->label('Name')
                    ->placeholder('Anonymous')
                    ->searchable(),

                TextColumn::make('respondent_email')
                    ->label('Email')
                    ->placeholder('Not provided')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('answers_count')
                    ->label('Answers')
                    ->counts('answers'),

                TextColumn::make('formatted_time_to_complete')
                    ->label('Duration')
                    ->toggleable(),

                TextColumn::make('submitted_at')
                    ->label('Submitted')
                    ->dateTime('M d, Y g:i A')
                    ->sortable(),

                // IP column removed
            ])
            ->defaultSort('submitted_at', 'desc')
            ->filters([
                Filter::make('completed')
                    ->label('Completed Only')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('submitted_at')),

                Filter::make('incomplete')
                    ->label('Incomplete Only')
                    ->query(fn(Builder $query): Builder => $query->whereNull('submitted_at')),
            ])
            ->headerActions([
                Action::make('export_excel')
                    ->label('Export Excel')
                    ->icon('heroicon-o-table-cells')
                    ->color('success')
                    ->url(fn() => route('admin.surveys.export.excel', $this->getOwnerRecord()))
                    ->openUrlInNewTab(),

                Action::make('export_pdf')
                    ->label('Export PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('danger')
                    ->url(fn() => route('admin.surveys.export.pdf', $this->getOwnerRecord()))
                    ->openUrlInNewTab(),
            ])
            ->actions([
                ViewAction::make()
                    ->slideOver(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }
}
