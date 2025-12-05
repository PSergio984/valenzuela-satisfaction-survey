<?php

namespace App\Filament\Admin\Resources\Surveys\Pages;

use App\Filament\Admin\Resources\Surveys\SurveyResource;
use App\Services\QrCodeService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSurvey extends ViewRecord
{
    protected static string $resource = SurveyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('qr_code')
                ->label('QR Code')
                ->icon('heroicon-o-qr-code')
                ->color('gray')
                ->modalHeading(fn(): string => "QR Code: {$this->record->title}")
                ->modalContent(function (): \Illuminate\Contracts\View\View {
                    $qrService = app(QrCodeService::class);

                    return view('filament.modals.qr-code', [
                        'survey' => $this->record,
                        'qrCode' => $qrService->generateSvg($this->record, 300),
                        'surveyUrl' => $qrService->getSurveyUrl($this->record),
                    ]);
                })
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close'),

            Action::make('export_excel')
                ->label('Export Excel')
                ->icon('heroicon-o-table-cells')
                ->color('success')
                ->url(fn() => route('admin.surveys.export.excel', $this->record))
                ->openUrlInNewTab(),

            Action::make('export_pdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->url(fn() => route('admin.surveys.export.pdf', $this->record))
                ->openUrlInNewTab(),

            EditAction::make(),
        ];
    }
}
