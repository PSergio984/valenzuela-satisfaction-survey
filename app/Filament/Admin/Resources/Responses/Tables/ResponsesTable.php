<?php

namespace App\Filament\Admin\Resources\Responses\Tables;

use App\Models\Response;
use App\Services\ResponseExportService;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
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

                TextColumn::make('answers_count')
                    ->label('Answers')
                    ->counts('answers'),

                TextColumn::make('ip_address')
                    ->label('IP')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('submitted_at')
                    ->label('Submitted')
                    ->dateTime('M d, Y H:i')
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
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('exportExcel')
                        ->label('Export to Excel')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('success')
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records): mixed {
                            /** @var Collection<int, Response> $records */
                            $exportService = app(ResponseExportService::class);
                            $filename = $exportService->generateFilename('responses', 'xlsx');

                            return $exportService->exportToExcel($records, $filename);
                        }),

                    BulkAction::make('exportCsv')
                        ->label('Export to CSV')
                        ->icon('heroicon-o-document-text')
                        ->color('gray')
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records): mixed {
                            /** @var Collection<int, Response> $records */
                            $exportService = app(ResponseExportService::class);
                            $filename = $exportService->generateFilename('responses', 'csv');

                            return $exportService->exportToCsv($records, $filename);
                        }),

                    BulkAction::make('exportPdf')
                        ->label('Export to PDF')
                        ->icon('heroicon-o-document')
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
