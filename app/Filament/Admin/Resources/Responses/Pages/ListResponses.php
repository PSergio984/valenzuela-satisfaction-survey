<?php

namespace App\Filament\Admin\Resources\Responses\Pages;

use App\Filament\Admin\Resources\Responses\ResponseResource;
use App\Models\Response;
use App\Services\ResponseExportService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListResponses extends ListRecords
{
    protected static string $resource = ResponseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('exportAllExcel')
                    ->label('Export All to Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function (): mixed {
                        $responses = Response::with(['survey', 'answers.question'])->get();
                        $exportService = app(ResponseExportService::class);
                        $filename = $exportService->generateFilename('all_responses', 'xlsx');

                        return $exportService->exportToExcel($responses, $filename);
                    }),

                Action::make('exportAllCsv')
                    ->label('Export All to CSV')
                    ->icon('heroicon-o-document-text')
                    ->color('gray')
                    ->action(function (): mixed {
                        $responses = Response::with(['survey', 'answers.question'])->get();
                        $exportService = app(ResponseExportService::class);
                        $filename = $exportService->generateFilename('all_responses', 'csv');

                        return $exportService->exportToCsv($responses, $filename);
                    }),

                Action::make('exportAllPdf')
                    ->label('Export All to PDF')
                    ->icon('heroicon-o-document')
                    ->color('danger')
                    ->action(function (): mixed {
                        $responses = Response::with(['survey', 'answers.question'])->get();
                        $exportService = app(ResponseExportService::class);
                        $filename = $exportService->generateFilename('all_responses', 'pdf');

                        return $exportService->exportToPdf($responses, $filename);
                    }),
            ])
                ->label('Export')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('primary')
                ->button(),

            CreateAction::make(),
        ];
    }
}
