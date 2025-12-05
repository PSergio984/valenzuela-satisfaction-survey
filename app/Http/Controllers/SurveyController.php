<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSurveyResponseRequest;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Response;
use App\Models\Survey;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class SurveyController extends Controller
{
    /**
     * Display a list of active surveys.
     */
    public function index(): InertiaResponse
    {
        $surveys = Survey::query()
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            })
            ->orderBy('created_at', 'desc')
            ->get(['id', 'title', 'description', 'slug']);

        return Inertia::render('surveys/index', [
            'surveys' => $surveys,
        ]);
    }

    /**
     * Display the survey form.
     */
    public function show(Survey $survey): InertiaResponse|RedirectResponse
    {
        // Check if survey is active and within date range
        if (! $survey->isOpen()) {
            return redirect()->route('surveys.index')
                ->with('error', 'This survey is not currently available.');
        }

        $survey->load(['questions' => function ($query) {
            $query->orderBy('order')->with('options');
        }]);

        return Inertia::render('surveys/show', [
            'survey' => $survey,
        ]);
    }

    /**
     * Store a new survey response.
     */
    public function store(StoreSurveyResponseRequest $request, Survey $survey): RedirectResponse
    {
        // Check if survey is still open
        if (! $survey->isOpen()) {
            return redirect()->route('surveys.index')
                ->with('error', 'This survey is no longer accepting responses.');
        }

        $validated = $request->validated();

        // Create the response
        $response = Response::create([
            'survey_id' => $survey->id,
            'respondent_name' => $validated['respondent_name'] ?? null,
            'respondent_email' => $validated['respondent_email'] ?? null,
            'ip_address' => $request->ip(),
            'submitted_at' => now(),
        ]);

        // Create answers for each question
        foreach ($validated['answers'] as $questionId => $answerValue) {
            $question = Question::find($questionId);

            if (! $question) {
                continue;
            }

            $answerData = [
                'response_id' => $response->id,
                'question_id' => $questionId,
            ];

            // Handle different answer types
            if (is_array($answerValue)) {
                // Checkbox or multi-select - store as selected_options
                $answerData['selected_options'] = $answerValue;
                $answerData['value'] = null;
            } else {
                // Text, radio, rating, select - store as value
                $answerData['value'] = $answerValue;
                $answerData['selected_options'] = null;
            }

            Answer::create($answerData);
        }

        return redirect()->route('surveys.thank-you', $survey)
            ->with('success', 'Thank you for your feedback!');
    }

    /**
     * Display the thank you page.
     */
    public function thankYou(Survey $survey): InertiaResponse
    {
        return Inertia::render('surveys/thank-you', [
            'survey' => $survey->only(['id', 'title', 'slug']),
        ]);
    }
}
