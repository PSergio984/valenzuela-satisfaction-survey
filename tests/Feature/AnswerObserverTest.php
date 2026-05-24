<?php

use App\Models\Answer;
use App\Models\Question;
use App\Models\Response;
use App\Models\Survey;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

test('a notification is sent when a low rating is submitted', function () {
    Notification::fake();
    
    // Create survey owner
    $owner = User::factory()->create();
    
    // Create survey with a rating question
    $survey = Survey::factory()->create(['created_by' => $owner->id]);
    $question = Question::factory()->create([
        'survey_id' => $survey->id,
        'type' => Question::TYPE_RATING,
    ]);
    
    // Create response
    $response = Response::factory()->create(['survey_id' => $survey->id]);

    // Submit a low rating (1 star)
    Answer::factory()->create([
        'response_id' => $response->id,
        'question_id' => $question->id,
        'value' => '1',
    ]);

    // Assert notification was sent via Laravel's system
    Notification::assertSentTo(
        $owner,
        \Filament\Notifications\DatabaseNotification::class
    );
});

test('no notification is sent when a high rating is submitted', function () {
    Notification::fake();
    
    $owner = User::factory()->create();
    $survey = Survey::factory()->create(['created_by' => $owner->id]);
    $question = Question::factory()->create([
        'survey_id' => $survey->id,
        'type' => Question::TYPE_RATING,
    ]);
    $response = Response::factory()->create(['survey_id' => $survey->id]);

    // Submit a high rating (5 stars)
    Answer::factory()->create([
        'response_id' => $response->id,
        'question_id' => $question->id,
        'value' => '5',
    ]);

    Notification::assertNothingSentTo($owner);
});
