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
use Filament\Tables\Filters\SelectFilter;
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
                Filter::make('submitted_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from')
                            ->label('From Date'),
                        \Filament\Forms\Components\DatePicker::make('until')
                            ->label('Until Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('submitted_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('submitted_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from'] ?? null) {
                            $indicators[] = 'From ' . \Carbon\Carbon::parse($data['from'])->toFormattedDateString();
                        }
                        if ($data['until'] ?? null) {
                            $indicators[] = 'Until ' . \Carbon\Carbon::parse($data['until'])->toFormattedDateString();
                        }
                        return $indicators;
                    }),

                Filter::make('today')
                    ->label('Today')
                    ->query(fn (Builder $query): Builder => $query->whereDate('submitted_at', today())),

                Filter::make('this_week')
                    ->label('This Week')
                    ->query(fn (Builder $query): Builder => $query->whereBetween('submitted_at', [now()->startOfWeek(), now()->endOfWeek()])),

                Filter::make('this_month')
                    ->label('This Month')
                    ->query(fn (Builder $query): Builder => $query->whereMonth('submitted_at', now()->month)
                        ->whereYear('submitted_at', now()->year)),

                SelectFilter::make('has_name')
                    ->label('Has Respondent Name')
                    ->options([
                        'yes' => 'Yes',
                        'no' => 'No (Anonymous)',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['value'] === 'yes') {
                            return $query->whereNotNull('respondent_name');
                        } elseif ($data['value'] === 'no') {
                            return $query->whereNull('respondent_name');
                        }
                        return $query;
                    }),

                SelectFilter::make('has_email')
                    ->label('Has Email')
                    ->options([
                        'yes' => 'Yes',
                        'no' => 'No',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['value'] === 'yes') {
                            return $query->whereNotNull('respondent_email');
                        } elseif ($data['value'] === 'no') {
                            return $query->whereNull('respondent_email');
                        }
                        return $query;
                    }),
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
                    ->slideOver()
                    ->modalWidth('sm'),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }
}
