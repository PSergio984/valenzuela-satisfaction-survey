<?php

use App\Models\Question;
use App\Models\Response;
use App\Models\Survey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class, WithoutMiddleware::class);

test('public survey list returns exactly 12 items per page', function () {
    Survey::factory()->count(15)->create([
        'is_active' => true,
        'is_public' => true,
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addDay(),
    ]);

    $response = $this->get(route('surveys.index'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('surveys/index')
        ->has('surveys.data', 12)
        ->has('surveys.links')
    );
});

test('store correctly uses started_at from payload for duration', function () {
    $survey = Survey::factory()->create([
        'is_active' => true,
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addDay(),
    ]);

    $startedAt = now()->subSeconds(120)->toIso8601String();

    $question = Question::factory()->create(['survey_id' => $survey->id, 'type' => 'text']);

    $response = $this->postJson(route('surveys.store', $survey->slug), [
        'respondent_name' => 'Test User',
        'started_at' => $startedAt,
        'answers' => [(string) $question->id => 'Test answer'],
    ]);

    $response->assertStatus(302); // Redirect to thank you page

    $recordedResponse = Response::where('survey_id', $survey->id)->first();

    expect($recordedResponse)->not->toBeNull();
    // Duration should be around 120 seconds
    expect($recordedResponse->time_to_complete)->toBeGreaterThanOrEqual(120);
    expect($recordedResponse->started_at->toIso8601String())->toBe($startedAt);
});
