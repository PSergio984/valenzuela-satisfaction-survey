<?php

namespace App\Filament\Admin\Resources\Responses\Pages;

use App\Filament\Admin\Resources\Responses\ResponseResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListResponses extends ListRecords
{
    protected static string $resource = ResponseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportAllExcel')
                ->label('Export Excel')
                ->icon('heroicon-o-table-cells')
                ->color('success')
                ->url(route('admin.responses.export.excel'))
                ->openUrlInNewTab(),

            Action::make('exportAllPdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->url(route('admin.responses.export.pdf'))
                ->openUrlInNewTab(),
        ];
    }
}
