<?php

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\Question;
use App\Models\Response;
use App\Models\Survey;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class ResponseSeeder extends Seeder
{
    /**
     * Filipino first names for realistic respondent data.
     *
     * @var array<int, string>
     */
    protected array $firstNames = [
        'Juan',
        'Maria',
        'Jose',
        'Ana',
        'Pedro',
        'Rosa',
        'Carlos',
        'Elena',
        'Miguel',
        'Sofia',
        'Antonio',
        'Isabella',
        'Fernando',
        'Camille',
        'Rafael',
        'Patricia',
        'Gabriel',
        'Andrea',
        'Manuel',
        'Christine',
        'Ricardo',
        'Angela',
        'Eduardo',
        'Jennifer',
        'Francisco',
        'Michelle',
        'Roberto',
        'Nicole',
        'Alejandro',
        'Jessica',
        'Marco',
        'Kathleen',
        'Daniel',
        'Stephanie',
        'Vincent',
        'Catherine',
        'Jerome',
        'Angelica',
        'Benedict',
        'Trisha',
        'Christian',
        'Jasmine',
        'Kenneth',
        'Marianne',
        'Ryan',
        'Denise',
        'Mark',
        'Kristine',
        'John',
        'Joyce',
    ];

    /**
     * Filipino last names for realistic respondent data.
     *
     * @var array<int, string>
     */
    protected array $lastNames = [
        'Santos',
        'Reyes',
        'Cruz',
        'Bautista',
        'Ocampo',
        'Garcia',
        'Mendoza',
        'Torres',
        'Gonzales',
        'Hernandez',
        'Lopez',
        'Martinez',
        'Rodriguez',
        'Ramos',
        'Aquino',
        'Castro',
        'Rivera',
        'Flores',
        'Villanueva',
        'Dela Cruz',
        'Del Rosario',
        'Soriano',
        'Fernandez',
        'Jimenez',
        'Diaz',
        'Aguilar',
        'Pascual',
        'Francisco',
        'Manalo',
        'Perez',
        'Morales',
        'De Guzman',
        'Navarro',
        'De Leon',
        'Castillo',
        'Salvador',
        'Domingo',
        'Santiago',
        'Valdez',
        'Tan',
        'Lim',
        'Chua',
        'Ong',
        'Sy',
        'Go',
    ];

    /**
     * Sample feedback comments for textarea questions.
     *
     * @var array<int, string>
     */
    protected array $feedbackComments = [
        'The service was excellent! Very professional staff.',
        'Could improve the waiting time. Too long.',
        'Very satisfied with the overall experience.',
        'Staff was helpful and courteous.',
        'The process was smooth and efficient.',
        'Need more staff during peak hours.',
        'Great improvement from last year!',
        'The online system needs improvement.',
        'Excellent customer service, very accommodating.',
        'Please consider extending office hours.',
        'The facilities are clean and well-maintained.',
        'More parking space would be helpful.',
        'Very responsive to inquiries.',
        'The queue system needs improvement.',
        'Fast and efficient service!',
        'Would appreciate more clear signage.',
        'The new system is much better.',
        'Please add more payment options.',
        'Very helpful staff, thank you!',
        'Consider having a mobile app.',
        'Good experience overall.',
        'The air conditioning was too cold.',
        'Need better ventilation in waiting area.',
        'Staff was very patient with my questions.',
        'Salamat po sa magandang serbisyo!',
        'Sana mas mapabilis pa ang proseso.',
        'Napakabait ng mga empleyado dito.',
        'Maganda na ang serbisyo, sana mapanatili.',
        '',
        '',
        '',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $surveys = Survey::with(['questions.options'])->get();

        if ($surveys->isEmpty()) {
            $this->command->warn('No surveys found. Please run SurveySeeder first.');
            return;
        }

        $totalResponses = 0;

        foreach ($surveys as $survey) {
            // Determine number of responses based on survey type
            $responseCount = $this->getResponseCountForSurvey($survey);
            $responses = $this->generateResponses($survey, $responseCount);
            $totalResponses += $responses->count();

            // Simulate analytics: views_count, starts_count
            // views_count: random 10-30% higher than starts_count
            $startsCount = $responses->count() + fake()->numberBetween(0, (int)($responses->count() * 0.15));
            $viewsCount = $startsCount + fake()->numberBetween((int)($startsCount * 0.1), (int)($startsCount * 0.3));

            $survey->update([
                'starts_count' => $startsCount,
                'views_count' => $viewsCount,
            ]);

            $this->command->info("Generated {$responses->count()} responses for: {$survey->title} (Views: {$viewsCount}, Starts: {$startsCount})");
        }

        $this->command->newLine();
        $this->command->info('Response seeding completed!');
        $this->command->info("Total responses created: {$totalResponses}");
    }

    /**
     * Determine how many responses to generate for a survey.
     */
    protected function getResponseCountForSurvey(Survey $survey): int
    {
        // Main customer satisfaction survey gets the most responses
        if (str_contains(strtolower($survey->title), 'customer satisfaction')) {
            return 150;
        }

        // Government services survey
        if (str_contains(strtolower($survey->title), 'government')) {
            return 100;
        }

        // Employee survey (fewer responses)
        if (str_contains(strtolower($survey->title), 'employee')) {
            return 40;
        }

        // Event feedback (past event)
        if (str_contains(strtolower($survey->title), 'event') || str_contains(strtolower($survey->title), 'araw')) {
            return 75;
        }

        // Website survey
        if (str_contains(strtolower($survey->title), 'website')) {
            return 60;
        }

        // Training evaluation
        if (str_contains(strtolower($survey->title), 'training')) {
            return 35;
        }

        // Default
        return 50;
    }

    /**
     * Generate responses for a survey.
     *
     * @return Collection<int, Response>
     */
    protected function generateResponses(Survey $survey, int $count): Collection
    {
        $responses = collect();
        $questions = $survey->questions;

        if ($questions->isEmpty()) {
            return $responses;
        }

        // Determine date range for responses
        $dateRange = $this->getDateRangeForSurvey($survey);


        for ($i = 0; $i < $count; $i++) {
            // Generate a weighted random date (more recent dates are more likely)
            $submittedAt = $this->getWeightedRandomDate($dateRange['start'], $dateRange['end']);

            // Simulate started_at and time_to_complete (2-8 min typical, some outliers)
            $minSeconds = 90; // 1.5 min
            $maxSeconds = 600; // 10 min
            $timeToComplete = fake()->numberBetween($minSeconds, $maxSeconds);
            // 10% chance of a long response (10-20 min)
            if (fake()->boolean(10)) {
                $timeToComplete = fake()->numberBetween(600, 1200);
            }
            $startedAt = (clone $submittedAt)->subSeconds($timeToComplete);

            $response = Response::create([
                'survey_id' => $survey->id,
                'user_id' => null,
                'respondent_name' => $survey->collect_respondent_info ? $this->getRandomName() : null,
                'respondent_email' => $survey->collect_respondent_info ? $this->getRandomEmail() : null,
                'respondent_phone' => $survey->collect_respondent_info && fake()->boolean(30) ? $this->getRandomPhone() : null,
                'user_agent' => fake()->userAgent(),
                'started_at' => $startedAt,
                'submitted_at' => $submittedAt,
                'time_to_complete' => $timeToComplete,
            ]);

            // Create answers for each question
            foreach ($questions as $question) {
                $this->createAnswerForQuestion($response, $question);
            }

            $responses->push($response);
        }

        return $responses;
    }

    /**
     * Get date range for survey responses.
     *
     * @return array{start: Carbon, end: Carbon}
     */
    protected function getDateRangeForSurvey(Survey $survey): array
    {
        // If survey has defined date range, use it
        if ($survey->starts_at && $survey->ends_at) {
            return [
                'start' => Carbon::parse($survey->starts_at),
                'end' => Carbon::parse($survey->ends_at),
            ];
        }

        // For inactive surveys, use past dates
        if (! $survey->is_active) {
            return [
                'start' => now()->subDays(60),
                'end' => now()->subDays(15),
            ];
        }

        // For active surveys, spread across last 60 days
        return [
            'start' => now()->subDays(60),
            'end' => now(),
        ];
    }

    /**
     * Get a weighted random date (more recent dates have higher probability).
     */
    protected function getWeightedRandomDate(Carbon $start, Carbon $end): Carbon
    {
        $totalDays = $start->diffInDays($end);

        if ($totalDays <= 0) {
            return $end;
        }

        // Use exponential distribution to favor recent dates
        $random = fake()->randomFloat(2, 0, 1);
        $weighted = pow($random, 0.5); // Square root makes it favor recent dates
        $daysToAdd = (int) ($weighted * $totalDays);

        $date = $start->copy()->addDays($daysToAdd);

        // Add random time
        $date->setTime(
            fake()->numberBetween(7, 18),
            fake()->numberBetween(0, 59),
            fake()->numberBetween(0, 59)
        );

        return $date;
    }

    /**
     * Create an answer for a specific question.
     */
    protected function createAnswerForQuestion(Response $response, Question $question): Answer
    {
        $value = null;
        $selectedOptions = null;

        switch ($question->type) {
            case Question::TYPE_RATING:
                // Generate weighted rating (slightly favoring positive)
                $value = $this->getWeightedRating();
                break;

            case Question::TYPE_TEXT:
                $value = fake()->boolean(80) ? fake()->sentence(fake()->numberBetween(3, 8)) : null;
                break;

            case Question::TYPE_TEXTAREA:
                $value = fake()->boolean(60) ? $this->feedbackComments[array_rand($this->feedbackComments)] : null;
                break;

            case Question::TYPE_RADIO:
            case Question::TYPE_SELECT:
                $options = $question->options;
                if ($options->isNotEmpty()) {
                    $selectedOption = $options->random();
                    $selectedOptions = [$selectedOption->id];
                    $value = $selectedOption->label;
                }
                break;

            case Question::TYPE_CHECKBOX:
                $options = $question->options;
                if ($options->isNotEmpty()) {
                    // Select 1-3 random options
                    $count = min(fake()->numberBetween(1, 3), $options->count());
                    $selected = $options->random($count);
                    $selectedOptions = $selected instanceof \App\Models\Option
                        ? [$selected->id]
                        : $selected->pluck('id')->toArray();
                    $value = $selected instanceof \App\Models\Option
                        ? $selected->label
                        : $selected->pluck('label')->implode(', ');
                }
                break;

            case Question::TYPE_NUMBER:
                $value = (string) fake()->numberBetween(1, 100);
                break;

            case Question::TYPE_DATE:
                $value = fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d');
                break;
        }

        return Answer::create([
            'response_id' => $response->id,
            'question_id' => $question->id,
            'value' => $value,
            'selected_options' => $selectedOptions,
        ]);
    }

    /**
     * Get a weighted rating that slightly favors positive ratings.
     * Distribution: 1: 5%, 2: 10%, 3: 20%, 4: 35%, 5: 30%
     */
    protected function getWeightedRating(): string
    {
        $rand = fake()->numberBetween(1, 100);

        if ($rand <= 5) {
            return '1';
        }
        if ($rand <= 15) {
            return '2';
        }
        if ($rand <= 35) {
            return '3';
        }
        if ($rand <= 70) {
            return '4';
        }

        return '5';
    }

    /**
     * Generate a random Filipino name.
     */
    protected function getRandomName(): string
    {
        return $this->firstNames[array_rand($this->firstNames)] . ' ' . $this->lastNames[array_rand($this->lastNames)];
    }

    /**
     * Generate a random email based on common patterns.
     */
    protected function getRandomEmail(): string
    {
        $firstName = strtolower($this->firstNames[array_rand($this->firstNames)]);
        $lastName = strtolower(str_replace(' ', '', $this->lastNames[array_rand($this->lastNames)]));
        $domains = ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com', 'mail.com'];

        $patterns = [
            "{$firstName}.{$lastName}",
            "{$firstName}{$lastName}",
            "{$firstName}_{$lastName}",
            $firstName . fake()->numberBetween(1, 999),
            "{$firstName}.{$lastName}" . fake()->numberBetween(1, 99),
        ];

        return $patterns[array_rand($patterns)] . '@' . $domains[array_rand($domains)];
    }

    /**
     * Generate a random Philippine mobile number.
     */
    protected function getRandomPhone(): string
    {
        $prefixes = ['0917', '0918', '0919', '0920', '0921', '0927', '0928', '0929', '0930', '0935', '0936', '0945', '0955', '0956', '0977', '0978', '0995', '0996', '0997'];

        return $prefixes[array_rand($prefixes)] . fake()->numerify('#######');
    }
}
