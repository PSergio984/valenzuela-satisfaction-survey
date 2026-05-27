<?php

use App\Http\Controllers\ResponseExportController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\SurveyExportController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

// Redirect /login to Filament's login
Route::get('/login', function () {
    return redirect('/admin/login');
})->name('login');

Route::middleware(['auth', 'verified'])->group(function () {
    // Admin Survey Export Routes
    Route::prefix('admin/surveys/{survey}')->name('admin.surveys.')->group(function () {
        Route::get('/export/excel', [SurveyExportController::class, 'exportExcel'])->name('export.excel');
        Route::get('/export/pdf', [SurveyExportController::class, 'exportPdf'])->name('export.pdf');
    });

    Route::get('admin/exports/download', [SurveyExportController::class, 'downloadExport'])->name('admin.exports.download');

    // Admin All Responses Export Routes
    Route::prefix('admin/responses')->name('admin.responses.')->group(function () {
        Route::get('/export/excel', [ResponseExportController::class, 'exportExcel'])->name('export.excel');
        Route::get('/export/pdf', [ResponseExportController::class, 'exportPdf'])->name('export.pdf');
    });
});

// Public Survey Routes
Route::prefix('surveys')->name('surveys.')->group(function () {
    Route::get('/', [SurveyController::class, 'index'])->name('index');
    Route::get('/{survey:slug}', [SurveyController::class, 'show'])->name('show');
    Route::post('/{survey:slug}', [SurveyController::class, 'store'])->name('store');
    Route::get('/{survey:slug}/thank-you', [SurveyController::class, 'thankYou'])->name('thank-you');
});

require __DIR__.'/settings.php';

// Dynamic static asset server with CORS headers for sandboxed iframes (origin null)
Route::match(['get', 'options'], 'js/{path}', function ($path) {
    $basePath = realpath(resource_path('js-static')) . DIRECTORY_SEPARATOR;
    $file = realpath(resource_path('js-static/' . $path));

    if (!$file || !str_starts_with($file, $basePath) || !file_exists($file) || is_dir($file)) {
        abort(404);
    }

    $extension = pathinfo($file, PATHINFO_EXTENSION);
    $contentType = match ($extension) {
        'js' => 'application/javascript',
        'css' => 'text/css',
        'json', 'map' => 'application/json',
        default => mime_content_type($file) ?: 'application/octet-stream',
    };

    return response()->file($file, [
        'Content-Type' => $contentType,
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => 'GET, OPTIONS',
        'Access-Control-Allow-Headers' => '*',
    ]);
})->where('path', '.*');

