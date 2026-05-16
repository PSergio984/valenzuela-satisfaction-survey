<?php

use App\Models\Response;
use App\Models\Survey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('completion_rate is cached for 5 minutes', function () {
    $survey = Survey::factory()->create(['starts_count' => 10]);
    Response::factory()->count(5)->create([
        'survey_id' => $survey->id,
        'submitted_at' => now(),
    ]);

    // Initial calculation should be 50%
    expect($survey->completion_rate)->toBe(50.0);
    expect(Cache::has("survey.{$survey->id}.completion_rate"))->toBeTrue();

    // Adding more responses should not change the rate due to cache
    Response::factory()->count(5)->create([
        'survey_id' => $survey->id,
        'submitted_at' => now(),
    ]);

    expect($survey->completion_rate)->toBe(50.0);

    // Clear cache to verify it updates
    Cache::flush();
    expect($survey->completion_rate)->toBe(100.0);
});

test('average_completion_time is rounded and cached', function () {
    $survey = Survey::factory()->create();

    // Average will be 150.5
    Response::factory()->create(['survey_id' => $survey->id, 'time_to_complete' => 100]);
    Response::factory()->create(['survey_id' => $survey->id, 'time_to_complete' => 201]);

    expect($survey->average_completion_time)->toBe(151); // Rounded from 150.5
    expect(Cache::has("survey.{$survey->id}.avg_completion_time"))->toBeTrue();
});
