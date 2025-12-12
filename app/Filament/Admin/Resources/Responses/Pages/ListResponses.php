<?php

namespace App\Filament\Admin\Resources\Responses\Pages;

use App\Filament\Admin\Resources\Responses\ResponseResource;
use App\Models\Survey;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;

class ListResponses extends ListRecords
{
    protected static string $resource = ResponseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportExcel')
                ->label('Export Excel')
                ->icon('heroicon-o-table-cells')
                ->color('success')
                ->form([
                    Select::make('survey_id')
                        ->label('Filter by Survey')
                        ->placeholder('All Surveys')
                        ->options(Survey::whereHas('responses')->pluck('title', 'id'))
                        ->searchable(),
                ])
                ->action(function (array $data) {
                    $url = route('admin.responses.export.excel');
                    if (! empty($data['survey_id'])) {
                        $url .= '?survey='.$data['survey_id'];
                    }

                    return redirect($url);
                }),

            Action::make('exportPdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->form([
                    Select::make('survey_id')
                        ->label('Select Survey')
                        ->options(Survey::whereHas('responses')->pluck('title', 'id'))
                        ->searchable()
                        ->required(),
                ])
                ->action(function (array $data) {
                    $url = route('admin.responses.export.pdf').'?survey='.$data['survey_id'];

                    return redirect($url);
                }),
        ];
    }
}
