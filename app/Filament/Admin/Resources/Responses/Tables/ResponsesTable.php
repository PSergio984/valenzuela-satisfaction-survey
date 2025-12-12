<?php

namespace App\Filament\Admin\Resources\Responses\Tables;

use App\Models\Response;
use App\Services\ResponseExportService;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
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
                        ->action(function (Collection $records): mixed {
                            /** @var Collection<int, Response> $records */
                            $exportService = app(ResponseExportService::class);
                            $filename = $exportService->generateFilename('responses', 'xlsx');

                            return $exportService->exportToExcel($records, $filename);
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
