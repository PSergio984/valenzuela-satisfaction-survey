<?php

use App\Models\Response;
use App\Models\Survey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

test('duration is tracked when response is submitted', function () {
    $survey = Survey::factory()->create();

    $startTime = now()->subMinutes(5);
    Carbon::setTestNow($startTime);

    $response = Response::factory()->create([
        'survey_id' => $survey->id,
        'started_at' => $startTime,
        'submitted_at' => null,
    ]);

    Carbon::setTestNow($startTime->copy()->addMinutes(5));

    $response->markAsSubmitted();

    expect($response->refresh()->time_to_complete)->toBe(300);
});

test('duration tracking is recorded via controller', function () {
    $survey = Survey::factory()->create(['is_active' => true]);

    // Start survey session (would normally happen on GET show)
    $startTime = now()->subMinutes(2);
    Carbon::setTestNow($startTime);

    // Simulate submission
    Carbon::setTestNow($startTime->copy()->addMinutes(2));

    $response = $this->post(route('surveys.store', $survey->slug), [
        'respondent_name' => 'Test User',
        'answers' => [], // Empty answers for now
        // Usually some session or hidden field would track start time if it's not stored yet
    ]);

    // We expect the controller to have set the time_to_complete
    $recordedResponse = Response::where('survey_id', $survey->id)->first();
    // expect($recordedResponse->time_to_complete)->toBe(120);
})->todo();
