<?php

namespace App\Filament\Admin\Resources\Surveys\Widgets;

use App\Models\Survey;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SurveyRatingsChart extends ChartWidget
{
    public ?Model $record = null;

    protected ?string $heading = 'Average Ratings by Question';

    protected ?string $description = 'Average rating score for each rating question';

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '400px';

    protected function getData(): array
    {
        /** @var Survey $survey */
        $survey = $this->record;

        if (! $survey) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        // Get all rating questions for this survey
        $ratingQuestions = $survey->questions()
            ->where('type', 'rating')
            ->orderBy('order')
            ->get();

        if ($ratingQuestions->isEmpty()) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $labels = [];
        $averages = [];
        $backgroundColors = [];

        foreach ($ratingQuestions as $question) {
            // Truncate question text for label
            $labels[] = Str::limit($question->question, 40);

            // Calculate average rating for this question
            $avgRating = \App\Models\Answer::where('question_id', $question->id)
                ->whereHas('response', function ($query) use ($survey) {
                    $query->where('survey_id', $survey->id)
                        ->whereNotNull('submitted_at');
                })
                ->avg('value') ?? 0;

            $averages[] = round($avgRating, 2);

            // Color based on average rating
            $backgroundColors[] = $this->getRatingColor($avgRating);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Average Rating',
                    'data' => $averages,
                    'backgroundColor' => $backgroundColors,
                    'borderColor' => $backgroundColors,
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getRatingColor(float $rating): string
    {
        if ($rating >= 4.5) {
            return '#22c55e'; // green
        } elseif ($rating >= 3.5) {
            return '#84cc16'; // lime
        } elseif ($rating >= 2.5) {
            return '#eab308'; // yellow
        } elseif ($rating >= 1.5) {
            return '#f97316'; // orange
        } else {
            return '#ef4444'; // red
        }
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y', // Horizontal bar chart
            'scales' => [
                'x' => [
                    'beginAtZero' => true,
                    'max' => 5,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
        ];
    }
}
