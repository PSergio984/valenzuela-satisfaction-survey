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
