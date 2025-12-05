<?php

use App\Models\Answer;
use App\Models\Question;
use App\Models\Response;
use App\Models\Survey;
use App\Services\ResponseExportService;

describe('ResponseExportService', function () {
    describe('transformResponse', function () {
        it('transforms a response with basic fields', function () {
            $exportService = new ResponseExportService;
            $survey = Survey::factory()->create(['title' => 'Test Survey']);
            $response = Response::factory()->create([
                'survey_id' => $survey->id,
                'respondent_name' => 'John Doe',
                'respondent_email' => 'john@example.com',
                'respondent_phone' => '555-1234',
                'ip_address' => '127.0.0.1',
                'submitted_at' => now(),
            ]);

            $transformed = $exportService->transformResponse($response);

            expect($transformed)->toHaveKey('ID', $response->id)
                ->and($transformed)->toHaveKey('Survey', 'Test Survey')
                ->and($transformed)->toHaveKey('Respondent Name', 'John Doe')
                ->and($transformed)->toHaveKey('Respondent Email', 'john@example.com')
                ->and($transformed)->toHaveKey('Respondent Phone', '555-1234')
                ->and($transformed)->toHaveKey('IP Address', '127.0.0.1');
        });

        it('handles anonymous responses', function () {
            $exportService = new ResponseExportService;
            $survey = Survey::factory()->create();
            $response = Response::factory()->create([
                'survey_id' => $survey->id,
                'respondent_name' => null,
                'respondent_email' => null,
            ]);

            $transformed = $exportService->transformResponse($response);

            expect($transformed['Respondent Name'])->toBe('Anonymous')
                ->and($transformed['Respondent Email'])->toBe('');
        });

        it('includes answer values in transformation', function () {
            $exportService = new ResponseExportService;
            $survey = Survey::factory()->create();
            $question = Question::factory()->create([
                'survey_id' => $survey->id,
                'question' => 'How satisfied are you?',
                'type' => 'rating',
            ]);
            $response = Response::factory()->create(['survey_id' => $survey->id]);
            Answer::factory()->create([
                'response_id' => $response->id,
                'question_id' => $question->id,
                'value' => '5',
            ]);

            $transformed = $exportService->transformResponse($response);

            expect($transformed)->toHaveKey('How satisfied are you?', '5');
        });

        it('handles checkbox answers with multiple selected options', function () {
            $exportService = new ResponseExportService;
            $survey = Survey::factory()->create();
            $question = Question::factory()->create([
                'survey_id' => $survey->id,
                'question' => 'Select your preferences',
                'type' => 'checkbox',
            ]);
            $response = Response::factory()->create(['survey_id' => $survey->id]);
            Answer::factory()->create([
                'response_id' => $response->id,
                'question_id' => $question->id,
                'selected_options' => ['Option A', 'Option B', 'Option C'],
            ]);

            $transformed = $exportService->transformResponse($response);

            expect($transformed['Select your preferences'])->toBe('Option A, Option B, Option C');
        });
    });

    describe('transformResponses', function () {
        it('transforms multiple responses', function () {
            $exportService = new ResponseExportService;
            $survey = Survey::factory()->create();
            $responses = Response::factory()->count(3)->create(['survey_id' => $survey->id]);

            $transformed = $exportService->transformResponses($responses);

            expect($transformed)->toHaveCount(3)
                ->and($transformed[0])->toHaveKey('ID')
                ->and($transformed[1])->toHaveKey('Survey')
                ->and($transformed[2])->toHaveKey('Respondent Name');
        });

        it('handles empty collection', function () {
            $exportService = new ResponseExportService;
            $responses = Response::query()->whereRaw('1 = 0')->get();

            $transformed = $exportService->transformResponses($responses);

            expect($transformed)->toBeArray()->toBeEmpty();
        });
    });

    describe('getStandardizedHeaders', function () {
        it('returns base headers for responses without answers', function () {
            $exportService = new ResponseExportService;
            $survey = Survey::factory()->create();
            $responses = Response::factory()->count(2)->create(['survey_id' => $survey->id]);

            $headers = $exportService->getStandardizedHeaders($responses);

            expect($headers)->toContain('ID')
                ->and($headers)->toContain('Survey')
                ->and($headers)->toContain('Respondent Name')
                ->and($headers)->toContain('Respondent Email')
                ->and($headers)->toContain('Submitted At');
        });

        it('includes question headers from answers', function () {
            $exportService = new ResponseExportService;
            $survey = Survey::factory()->create();
            $question1 = Question::factory()->create([
                'survey_id' => $survey->id,
                'question' => 'First Question',
            ]);
            $question2 = Question::factory()->create([
                'survey_id' => $survey->id,
                'question' => 'Second Question',
            ]);
            $response = Response::factory()->create(['survey_id' => $survey->id]);
            Answer::factory()->create([
                'response_id' => $response->id,
                'question_id' => $question1->id,
            ]);
            Answer::factory()->create([
                'response_id' => $response->id,
                'question_id' => $question2->id,
            ]);

            $headers = $exportService->getStandardizedHeaders(collect([$response]));

            expect($headers)->toContain('First Question')
                ->and($headers)->toContain('Second Question');
        });

        it('does not duplicate question headers across responses', function () {
            $exportService = new ResponseExportService;
            $survey = Survey::factory()->create();
            $question = Question::factory()->create([
                'survey_id' => $survey->id,
                'question' => 'Shared Question',
            ]);
            $responses = Response::factory()->count(3)->create(['survey_id' => $survey->id]);

            foreach ($responses as $response) {
                Answer::factory()->create([
                    'response_id' => $response->id,
                    'question_id' => $question->id,
                ]);
            }

            $headers = $exportService->getStandardizedHeaders($responses);
            $questionCount = array_count_values($headers)['Shared Question'] ?? 0;

            expect($questionCount)->toBe(1);
        });
    });

    describe('normalizeRow', function () {
        it('fills missing keys with empty strings', function () {
            $exportService = new ResponseExportService;
            $row = ['ID' => 1, 'Survey' => 'Test'];
            $headers = ['ID', 'Survey', 'Respondent Name', 'Missing Field'];

            $normalized = $exportService->normalizeRow($row, $headers);

            expect($normalized)->toHaveKey('Missing Field', '')
                ->and($normalized)->toHaveKey('Respondent Name', '')
                ->and($normalized['ID'])->toBe(1)
                ->and($normalized['Survey'])->toBe('Test');
        });

        it('maintains order based on headers', function () {
            $exportService = new ResponseExportService;
            $row = ['C' => 3, 'A' => 1, 'B' => 2];
            $headers = ['A', 'B', 'C'];

            $normalized = $exportService->normalizeRow($row, $headers);
            $keys = array_keys($normalized);

            expect($keys[0])->toBe('A')
                ->and($keys[1])->toBe('B')
                ->and($keys[2])->toBe('C');
        });
    });

    describe('calculateStatistics', function () {
        it('calculates average ratings for rating questions', function () {
            $exportService = new ResponseExportService;
            $survey = Survey::factory()->create();
            $question = Question::factory()->create([
                'survey_id' => $survey->id,
                'question' => 'Rate our service',
                'type' => 'rating',
            ]);

            $responses = Response::factory()->count(3)->create(['survey_id' => $survey->id]);
            $ratings = [3, 4, 5];

            foreach ($responses as $index => $response) {
                Answer::factory()->create([
                    'response_id' => $response->id,
                    'question_id' => $question->id,
                    'value' => (string) $ratings[$index],
                ]);
            }

            $stats = $exportService->calculateStatistics($responses);

            expect($stats['averageRatings'])->toHaveKey('Rate our service')
                ->and($stats['averageRatings']['Rate our service'])->toBe(4.0)
                ->and($stats['overallAverage'])->toBe(4.0);
        });

        it('calculates date range from responses', function () {
            $exportService = new ResponseExportService;
            $survey = Survey::factory()->create();
            $responses = collect([
                Response::factory()->create([
                    'survey_id' => $survey->id,
                    'submitted_at' => now()->subDays(10),
                ]),
                Response::factory()->create([
                    'survey_id' => $survey->id,
                    'submitted_at' => now(),
                ]),
            ]);

            $stats = $exportService->calculateStatistics($responses);

            expect($stats['dateRange'])->toHaveKey('from')
                ->and($stats['dateRange'])->toHaveKey('to')
                ->and($stats['dateRange']['from'])->not->toBeNull()
                ->and($stats['dateRange']['to'])->not->toBeNull();
        });

        it('returns null overall average when no rating questions', function () {
            $exportService = new ResponseExportService;
            $survey = Survey::factory()->create();
            $question = Question::factory()->create([
                'survey_id' => $survey->id,
                'type' => 'text',
            ]);
            $response = Response::factory()->create(['survey_id' => $survey->id]);
            Answer::factory()->create([
                'response_id' => $response->id,
                'question_id' => $question->id,
                'value' => 'Some text answer',
            ]);

            $stats = $exportService->calculateStatistics(collect([$response]));

            expect($stats['overallAverage'])->toBeNull()
                ->and($stats['averageRatings'])->toBeEmpty();
        });
    });

    describe('generateFilename', function () {
        it('generates filename with timestamp', function () {
            $exportService = new ResponseExportService;
            $filename = $exportService->generateFilename('responses', 'xlsx');

            expect($filename)->toStartWith('responses_')
                ->and($filename)->toEndWith('.xlsx')
                ->and(strlen($filename))->toBeGreaterThan(20);
        });

        it('generates different filenames for different extensions', function () {
            $exportService = new ResponseExportService;
            $xlsx = $exportService->generateFilename('test', 'xlsx');
            $csv = $exportService->generateFilename('test', 'csv');
            $pdf = $exportService->generateFilename('test', 'pdf');

            expect($xlsx)->toEndWith('.xlsx')
                ->and($csv)->toEndWith('.csv')
                ->and($pdf)->toEndWith('.pdf');
        });
    });

    describe('exportToExcel', function () {
        it('returns a streamed response', function () {
            $exportService = new ResponseExportService;
            $survey = Survey::factory()->create();
            $responses = Response::factory()->count(2)->create(['survey_id' => $survey->id]);

            $result = $exportService->exportToExcel($responses, 'test.xlsx');

            expect($result)->toBeInstanceOf(\Symfony\Component\HttpFoundation\StreamedResponse::class);
        });
    });

    describe('exportToCsv', function () {
        it('returns a streamed response', function () {
            $exportService = new ResponseExportService;
            $survey = Survey::factory()->create();
            $responses = Response::factory()->count(2)->create(['survey_id' => $survey->id]);

            $result = $exportService->exportToCsv($responses, 'test.csv');

            expect($result)->toBeInstanceOf(\Symfony\Component\HttpFoundation\StreamedResponse::class);
        });
    });
});

describe('Export Integration', function () {
    it('can transform responses with complex nested data', function () {
        $exportService = new ResponseExportService;
        $survey = Survey::factory()->create(['title' => 'Complex Survey']);

        // Create questions of different types
        $textQuestion = Question::factory()->create([
            'survey_id' => $survey->id,
            'question' => 'Text feedback',
            'type' => 'text',
        ]);
        $ratingQuestion = Question::factory()->create([
            'survey_id' => $survey->id,
            'question' => 'Rating',
            'type' => 'rating',
        ]);
        $checkboxQuestion = Question::factory()->create([
            'survey_id' => $survey->id,
            'question' => 'Multi-select',
            'type' => 'checkbox',
        ]);

        $response = Response::factory()->create([
            'survey_id' => $survey->id,
            'respondent_name' => 'Test User',
        ]);

        Answer::factory()->create([
            'response_id' => $response->id,
            'question_id' => $textQuestion->id,
            'value' => 'Great service!',
        ]);
        Answer::factory()->create([
            'response_id' => $response->id,
            'question_id' => $ratingQuestion->id,
            'value' => '5',
        ]);
        Answer::factory()->create([
            'response_id' => $response->id,
            'question_id' => $checkboxQuestion->id,
            'selected_options' => ['Fast', 'Friendly', 'Professional'],
        ]);

        $transformed = $exportService->transformResponse($response);

        expect($transformed['Text feedback'])->toBe('Great service!')
            ->and($transformed['Rating'])->toBe('5')
            ->and($transformed['Multi-select'])->toBe('Fast, Friendly, Professional');
    });

    it('handles large number of responses efficiently', function () {
        $exportService = new ResponseExportService;
        $survey = Survey::factory()->create();
        $responses = Response::factory()->count(50)->create(['survey_id' => $survey->id]);

        $startTime = microtime(true);
        $transformed = $exportService->transformResponses($responses);
        $endTime = microtime(true);

        expect($transformed)->toHaveCount(50)
            ->and($endTime - $startTime)->toBeLessThan(5); // Should complete in under 5 seconds
    });
});
