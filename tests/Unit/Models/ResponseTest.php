<?php

use App\Models\Response;
use App\Models\Survey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('markAsSubmitted calculates duration as an integer', function () {
    $survey = Survey::factory()->create();

    $startTime = now()->subSeconds(300);
    Carbon::setTestNow($startTime);

    $response = Response::factory()->create([
        'survey_id' => $survey->id,
        'started_at' => $startTime,
    ]);

    $submitTime = $startTime->copy()->addSeconds(300);
    Carbon::setTestNow($submitTime);

    $response->markAsSubmitted();

    expect($response->refresh()->time_to_complete)->toBe(300);
    expect($response->time_to_complete)->toBeInt();
});

test('formatted_time_to_complete handles different duration ranges', function () {
    $response = new Response;

    $response->time_to_complete = 45;
    expect($response->formatted_time_to_complete)->toBe('45 sec');

    $response->time_to_complete = 60;
    expect($response->formatted_time_to_complete)->toBe('1 min');

    $response->time_to_complete = 75;
    expect($response->formatted_time_to_complete)->toBe('1 min 15 sec');

    $response->time_to_complete = null;
    expect($response->formatted_time_to_complete)->toBe('N/A');
});
