<?php

use App\Models\Survey;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SurveyResponsesExport;

test('export survey responses job is dispatched', function () {
    Queue::fake();
    $user = User::factory()->create();
    $survey = Survey::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.surveys.export.excel', $survey));

    // This will fail because currently it's a direct download, not queued
    Queue::assertPushed(\App\Jobs\ExportSurveyResponsesJob::class);
})->todo();

test('export job saves file to private disk', function () {
    Storage::fake('private');
    $survey = Survey::factory()->create();
    
    // We'll need a job class later
    // $job = new \App\Jobs\ExportSurveyResponsesJob($survey, User::factory()->create());
    // $job->handle();
    
    // Storage::disk('private')->assertExists("exports/survey-{$survey->id}.xlsx");
})->todo();
