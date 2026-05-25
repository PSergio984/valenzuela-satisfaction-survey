<?php

namespace App\Http\Controllers;

use App\Exports\SurveyResponsesExport;
use App\Jobs\GenerateSurveyPdfReport;
use App\Models\Survey;
use Filament\Notifications\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SurveyExportController extends Controller
{
    /**
     * Export survey responses to Excel (Queued).
     */
    public function exportExcel(Survey $survey): RedirectResponse
    {
        $filename = "exports/survey-{$survey->slug}-responses-".now()->format('Y-m-d-His').'.xlsx';
        $userId = auth()->id();

        Excel::queue(
            new SurveyResponsesExport($survey->id, $userId, $filename),
            $filename,
            'private'
        );

        Notification::make()
            ->title('Export Queued')
            ->body('Your export is being processed and will be available in your notifications when ready.')
            ->info()
            ->send();

        return back();
    }

    /**
     * Download an export from the private disk.
     */
    public function downloadExport(Request $request): StreamedResponse
    {
        $path = $request->query('path');

        if (! $path || ! Storage::disk('private')->exists($path)) {
            abort(404, 'Export file not found.');
        }

        return Storage::disk('private')->download($path);
    }

    /**
     * Export survey responses to PDF (Queued).
     */
    public function exportPdf(Survey $survey): RedirectResponse
    {
        $filename = "exports/survey-{$survey->slug}-report-".now()->format('Y-m-d-His').'.pdf';
        $userId = auth()->id();

        GenerateSurveyPdfReport::dispatch(
            $survey->id,
            $userId,
            $filename
        );

        Notification::make()
            ->title('PDF Export Queued')
            ->body('Your PDF report is being generated and will be available in your notifications when ready.')
            ->info()
            ->send();

        return back();
    }
}
