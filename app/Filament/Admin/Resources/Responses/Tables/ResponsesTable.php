<?php

namespace App\Filament\Admin\Resources\Responses\Tables;

use App\Models\Response;
use App\Services\ResponseExportService;
use Carbon\Carbon;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ResponsesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('survey.title')
                    ->label('Survey')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                TextColumn::make('respondent_name')
                    ->label('Respondent')
                    ->searchable()
                    ->default('Anonymous'),

                TextColumn::make('respondent_email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // IP column removed

                TextColumn::make('submitted_at')
                    ->label('Submitted')
                    ->dateTime('M d, Y h:i A')
                    ->timezone('Asia/Manila')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('submitted_at', 'desc')
            ->filters([
                SelectFilter::make('survey')
                    ->relationship('survey', 'title'),

                Filter::make('submitted_at')
                    ->form([
                        DatePicker::make('from')
                            ->label('From Date'),
                        DatePicker::make('until')
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
                            $indicators[] = 'From '.Carbon::parse($data['from'])->toFormattedDateString();
                        }
                        if ($data['until'] ?? null) {
                            $indicators[] = 'Until '.Carbon::parse($data['until'])->toFormattedDateString();
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

                Filter::make('duration')
                    ->form([
                        TextInput::make('min_duration')
                            ->label('Min Duration (sec)')
                            ->numeric(),
                        TextInput::make('max_duration')
                            ->label('Max Duration (sec)')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_duration'],
                                fn (Builder $query, $duration): Builder => $query->where('time_to_complete', '>=', $duration),
                            )
                            ->when(
                                $data['max_duration'],
                                fn (Builder $query, $duration): Builder => $query->where('time_to_complete', '<=', $duration),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['min_duration'] ?? null) {
                            $indicators[] = 'Min Duration: '.$data['min_duration'].'s';
                        }
                        if ($data['max_duration'] ?? null) {
                            $indicators[] = 'Max Duration: '.$data['max_duration'].'s';
                        }

                        return $indicators;
                    }),

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

                Filter::make('rating')
                    ->form([
                        Select::make('min_rating')
                            ->options([1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5]),
                        Select::make('max_rating')
                            ->options([1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_rating'],
                                fn (Builder $query, $rating): Builder => $query->whereHas('answers', function ($q) use ($rating) {
                                    $q->whereHas('question', fn ($q) => $q->where('type', 'rating'))
                                        ->whereRaw('CAST(value AS INTEGER) >= ?', [$rating]);
                                }),
                            )
                            ->when(
                                $data['max_rating'],
                                fn (Builder $query, $rating): Builder => $query->whereHas('answers', function ($q) use ($rating) {
                                    $q->whereHas('question', fn ($q) => $q->where('type', 'rating'))
                                        ->whereRaw('CAST(value AS INTEGER) <= ?', [$rating]);
                                }),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['min_rating'] ?? null) {
                            $indicators[] = 'Min Rating: '.$data['min_rating'];
                        }
                        if ($data['max_rating'] ?? null) {
                            $indicators[] = 'Max Rating: '.$data['max_rating'];
                        }

                        return $indicators;
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('exportExcel')
                        ->label('Export to Excel')
                        ->icon('heroicon-o-table-cells')
                        ->color('success')
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records): void {
                            /** @var Collection<int, Response> $records */
                            $surveyId = $records->first()?->survey_id;

                            if (! $surveyId) {
                                Notification::make()
                                    ->title('Export Failed')
                                    ->body('Could not determine the survey for the selected responses.')
                                    ->danger()
                                    ->send();

                                return;
                            }

                            $exportService = app(ResponseExportService::class);
                            $filename = $exportService->generateFilename('responses', 'xlsx');

                            $exportService->queueExcelExport(
                                $surveyId,
                                auth()->id(),
                                $filename,
                                $records->pluck('id')->toArray()
                            );

                            Notification::make()
                                ->title('Export Started')
                                ->body('The export has been queued and you will be notified once it is ready.')
                                ->success()
                                ->send();
                        }),

                    BulkAction::make('exportPdf')
                        ->label('Export to PDF')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('danger')
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records): mixed {
                            /** @var Collection<int, Response> $records */
                            $exportService = app(ResponseExportService::class);
                            $filename = $exportService->generateFilename('responses', 'pdf');

                            return $exportService->exportToPdf($records, $filename);
                        }),

                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}


