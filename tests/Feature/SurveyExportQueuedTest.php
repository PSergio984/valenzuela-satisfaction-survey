<?php

use App\Exports\SurveyResponsesExport;
use App\Models\Response;
use App\Models\Survey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Maatwebsite\Excel\Facades\Excel;

uses(RefreshDatabase::class);

test('SurveyResponsesExport filters by response IDs', function () {
    $survey = Survey::factory()->create();
    $user = User::factory()->create();

    $responses = Response::factory()->count(5)->create([
        'survey_id' => $survey->id,
        'submitted_at' => now(),
    ]);

    $selectedIds = $responses->take(2)->pluck('id')->toArray();
    $export = new SurveyResponsesExport($survey->id, $user->id, 'test.xlsx', $selectedIds);

    $query = $export->query();

    expect($query->count())->toBe(2);
    expect($query->pluck('id')->toArray())->toEqualCanonicalizing($selectedIds);
});

/*
test('SurveyResponsesExport sends notification after export', function () {
    $survey = Survey::factory()->create(['title' => 'Notify Survey']);
    $user = User::factory()->create();
    $filename = 'notify.xlsx';

    // Clear notifications table
    \Illuminate\Support\Facades\DB::table('notifications')->truncate();

    $export = new SurveyResponsesExport($survey->id, $user->id, $filename);

    // Run the export (store it to trigger events)
    Excel::store($export, $filename, 'private');

    // Verify notification was sent to database
    $this->assertDatabaseHas('notifications', [
        'notifiable_id' => $user->id,
    ]);
})->todo();
*/
