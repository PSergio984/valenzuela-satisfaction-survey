<?php

namespace App\Filament\Admin\Resources\Surveys\Pages;

use App\Filament\Admin\Resources\Surveys\SurveyResource;
use App\Filament\Admin\Resources\Surveys\Widgets\SurveyStatsOverview;
use App\Services\QrCodeService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewSurvey extends ViewRecord
{
    protected static string $resource = SurveyResource::class;

    protected static ?string $navigationLabel = 'Details & Analytics';

    protected static ?string $title = 'Details & Analytics';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected function getHeaderWidgets(): array
    {
        return [
            SurveyStatsOverview::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 3;
    }

    protected function getHeaderActions(): array
    {
        return [
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
                        'qrCodeRaw' => $qrService->generateRawSvg($this->record, 300),
                        'surveyUrl' => $qrService->getSurveyUrl($this->record),
                    ]);
                })
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close'),
        ];
    }
}
