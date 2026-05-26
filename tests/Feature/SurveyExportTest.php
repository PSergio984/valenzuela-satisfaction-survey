<?php

use App\Exports\SurveyResponsesExport;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Response;
use App\Models\Survey;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

describe('Excel Export', function () {
    it('can export survey responses to excel', function () {
        Excel::fake();
        \Illuminate\Support\Carbon::setTestNow(now());

        // Create a survey with questions and responses
        $survey = Survey::factory()->create();

        // ... [rest of setup remains same] ...

        // Test the export route
        $exportResponse = $this->get(route('admin.surveys.export.excel', $survey));

        $exportResponse->assertRedirect();
        
        $filename = "exports/survey-{$survey->slug}-responses-".now()->format('Y-m-d-His').'.xlsx';
        Excel::assertQueued($filename, 'private');
    });

    it('returns correct headers in excel export', function () {
        $survey = Survey::factory()->create(['title' => 'Test Survey']);

        $question = Question::factory()->create([
            'survey_id' => $survey->id,
            'question' => 'Test Question',
            'order' => 1,
        ]);

        $export = new SurveyResponsesExport($survey->id, $this->user->id, 'test.xlsx');
        $headings = $export->headings();

        expect($headings)->toContain('ID')
            ->toContain('Submitted At')
            ->toContain('Respondent Name')
            ->toContain('Respondent Email')
            ->toContain('Duration')
            ->toContain('Test Question');
    });

    it('exports correct data for responses', function () {
        $survey = Survey::factory()->create();

        $question = Question::factory()->create([
            'survey_id' => $survey->id,
            'question' => 'Rate us',
            'type' => Question::TYPE_RATING,
            'order' => 1,
        ]);

        $response = Response::factory()->create([
            'survey_id' => $survey->id,
            'respondent_name' => 'Jane Smith',
            'respondent_email' => 'jane@example.com',
            'submitted_at' => now(),
        ]);

        Answer::factory()->create([
            'response_id' => $response->id,
            'question_id' => $question->id,
            'value' => '4',
        ]);

        $export = new SurveyResponsesExport($survey->id, $this->user->id, 'test.xlsx');
        $query = $export->query();
        $results = $query->get();

        expect($results)->toHaveCount(1);
        
        $row = $export->map($results->first());
        expect($row)->toContain('Jane Smith')
            ->toContain('jane@example.com')
            ->toContain('4');
    });

    it('handles surveys with no responses', function () {
        $survey = Survey::factory()->create();

        Question::factory()->create([
            'survey_id' => $survey->id,
            'order' => 1,
        ]);

        $export = new SurveyResponsesExport($survey->id, $this->user->id, 'test.xlsx');
        $results = $export->query()->get();

        expect($results)->toHaveCount(0);
    });

    it('handles checkbox questions with multiple selected options', function () {
        $survey = Survey::factory()->create();

        $question = Question::factory()->create([
            'survey_id' => $survey->id,
            'question' => 'Select your interests',
            'type' => Question::TYPE_CHECKBOX,
            'order' => 1,
        ]);

        $response = Response::factory()->create([
            'survey_id' => $survey->id,
            'submitted_at' => now(),
        ]);

        Answer::factory()->create([
            'response_id' => $response->id,
            'question_id' => $question->id,
            'selected_options' => ['Sports', 'Music', 'Reading'],
        ]);

        $export = new SurveyResponsesExport($survey->id, $this->user->id, 'test.xlsx');
        $results = $export->query()->get();
        $row = $export->map($results->first());

        expect($row)->toContain('Sports, Music, Reading');
    });

    it('requires authentication to export', function () {
        // Log out the user
        auth()->logout();

        $survey = Survey::factory()->create();

        $response = $this->get(route('admin.surveys.export.excel', $survey));

        $response->assertRedirect(route('login'));
    });
});

describe('PDF Export', function () {
    it('can export survey responses to pdf', function () {
        \Illuminate\Support\Facades\Queue::fake();

        $survey = Survey::factory()->create();

        Question::factory()->create([
            'survey_id' => $survey->id,
            'question' => 'How was your experience?',
            'type' => Question::TYPE_RATING,
            'order' => 1,
        ]);

        Response::factory()->count(3)->create([
            'survey_id' => $survey->id,
            'submitted_at' => now(),
        ]);

        $response = $this->get(route('admin.surveys.export.pdf', $survey));

        $response->assertRedirect();
        \Illuminate\Support\Facades\Queue::assertPushed(\App\Jobs\GenerateSurveyPdfReport::class);
    });

    it('requires authentication to export pdf', function () {
        auth()->logout();

        $survey = Survey::factory()->create();

        $response = $this->get(route('admin.surveys.export.pdf', $survey));

        $response->assertRedirect(route('login'));
    });
});
