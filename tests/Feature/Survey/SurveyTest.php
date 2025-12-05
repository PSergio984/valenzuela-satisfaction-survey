<?php

use App\Models\Answer;
use App\Models\Option;
use App\Models\Question;
use App\Models\Response;
use App\Models\Survey;
use App\Models\User;

test('can create a survey', function () {
    $user = User::factory()->create();
    $survey = Survey::factory()->create(['created_by' => $user->id]);

    expect($survey)->toBeInstanceOf(Survey::class)
        ->and($survey->title)->not->toBeEmpty()
        ->and($survey->slug)->not->toBeEmpty()
        ->and($survey->creator->id)->toBe($user->id);
});

test('survey generates slug automatically', function () {
    $survey = Survey::factory()->create([
        'title' => 'Test Survey Title',
        'slug' => null,
    ]);

    expect($survey->slug)->toContain('test-survey-title');
});

test('can create questions for a survey', function () {
    $survey = Survey::factory()->create();
    $question = Question::factory()->create(['survey_id' => $survey->id]);

    expect($survey->questions)->toHaveCount(1)
        ->and($survey->questions->first()->id)->toBe($question->id);
});

test('can create options for a question', function () {
    $question = Question::factory()->radio()->create();
    $options = Option::factory()->count(3)->create(['question_id' => $question->id]);

    expect($question->options)->toHaveCount(3);
});

test('question knows if it has options', function () {
    $radioQuestion = Question::factory()->radio()->create();
    $textQuestion = Question::factory()->text()->create();

    expect($radioQuestion->hasOptions())->toBeTrue()
        ->and($textQuestion->hasOptions())->toBeFalse();
});

test('survey knows if it is open', function () {
    $activeSurvey = Survey::factory()->active()->create([
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addDay(),
    ]);

    $inactiveSurvey = Survey::factory()->inactive()->create();

    $expiredSurvey = Survey::factory()->active()->create([
        'ends_at' => now()->subDay(),
    ]);

    expect($activeSurvey->isOpen())->toBeTrue()
        ->and($inactiveSurvey->isOpen())->toBeFalse()
        ->and($expiredSurvey->isOpen())->toBeFalse();
});

test('can create a response for a survey', function () {
    $survey = Survey::factory()->create();
    $response = Response::factory()->create(['survey_id' => $survey->id]);

    expect($survey->responses)->toHaveCount(1)
        ->and($response->survey->id)->toBe($survey->id);
});

test('authenticated user can access admin surveys page', function () {
    // Seed roles and permissions
    $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    // Assign super_admin role to access all permissions
    $user->assignRole('super_admin');

    $this->actingAs($user);

    $response = $this->get('/admin/surveys');

    // Filament may require specific authentication, so we check it's not a redirect to login
    $response->assertSuccessful();
});

test('guest cannot access admin surveys page', function () {
    $response = $this->get('/admin/surveys');

    $response->assertRedirect('/admin/login');
});

// Public Survey Page Tests

test('surveys index page can be accessed', function () {
    $response = $this->get('/surveys');

    $response->assertSuccessful();
    $response->assertInertia(fn($page) => $page->component('surveys/index'));
});

test('surveys index shows active surveys', function () {
    $activeSurvey = Survey::factory()->active()->create([
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addDay(),
    ]);

    $inactiveSurvey = Survey::factory()->inactive()->create();

    $response = $this->get('/surveys');

    $response->assertSuccessful();
    $response->assertInertia(
        fn($page) => $page
            ->component('surveys/index')
            ->has('surveys', 1)
            ->has(
                'surveys.0',
                fn($survey) => $survey
                    ->where('id', $activeSurvey->id)
                    ->where('title', $activeSurvey->title)
                    ->etc()
            )
    );
});

test('survey show page can be accessed for active survey', function () {
    $survey = Survey::factory()->active()->create([
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addDay(),
    ]);

    $response = $this->get("/surveys/{$survey->slug}");

    $response->assertSuccessful();
    $response->assertInertia(
        fn($page) => $page
            ->component('surveys/show')
            ->has(
                'survey',
                fn($s) => $s
                    ->where('id', $survey->id)
                    ->where('title', $survey->title)
                    ->etc()
            )
    );
});

test('survey show page redirects for inactive survey', function () {
    $survey = Survey::factory()->inactive()->create();

    $response = $this->get("/surveys/{$survey->slug}");

    $response->assertRedirect('/surveys');
});

test('can submit survey response', function () {
    $survey = Survey::factory()->active()->create([
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addDay(),
    ]);

    $textQuestion = Question::factory()->text()->required()->create([
        'survey_id' => $survey->id,
        'order' => 1,
    ]);

    $ratingQuestion = Question::factory()->rating()->create([
        'survey_id' => $survey->id,
        'order' => 2,
    ]);

    $response = $this->post("/surveys/{$survey->slug}", [
        'respondent_name' => 'Test User',
        'respondent_email' => 'test@example.com',
        'answers' => [
            $textQuestion->id => 'This is my feedback',
            $ratingQuestion->id => '5',
        ],
    ]);

    $response->assertRedirect("/surveys/{$survey->slug}/thank-you");

    // Check response was created
    expect(Response::count())->toBe(1);
    expect(Answer::count())->toBe(2);

    $surveyResponse = Response::first();
    expect($surveyResponse->respondent_name)->toBe('Test User');
    expect($surveyResponse->respondent_email)->toBe('test@example.com');
});

test('survey response validation for required questions', function () {
    $survey = Survey::factory()->active()->create([
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addDay(),
    ]);

    $requiredQuestion = Question::factory()->text()->required()->create([
        'survey_id' => $survey->id,
    ]);

    $response = $this->post("/surveys/{$survey->slug}", [
        'answers' => [],
    ]);

    $response->assertSessionHasErrors("answers.{$requiredQuestion->id}");
});

test('thank you page can be accessed after submission', function () {
    $survey = Survey::factory()->create();

    $response = $this->get("/surveys/{$survey->slug}/thank-you");

    $response->assertSuccessful();
    $response->assertInertia(
        fn($page) => $page
            ->component('surveys/thank-you')
            ->has(
                'survey',
                fn($s) => $s
                    ->where('id', $survey->id)
                    ->where('title', $survey->title)
                    ->etc()
            )
    );
});
