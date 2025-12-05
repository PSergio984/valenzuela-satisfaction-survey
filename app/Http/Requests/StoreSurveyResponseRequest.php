<?php

namespace App\Http\Requests;

use App\Models\Question;
use Illuminate\Foundation\Http\FormRequest;

class StoreSurveyResponseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Public surveys are accessible to everyone
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'respondent_name' => ['nullable', 'string', 'max:255'],
            'respondent_email' => ['nullable', 'email', 'max:255'],
            'answers' => ['required', 'array'],
        ];

        // Get the survey from the route
        $survey = $this->route('survey');

        if ($survey) {
            $questions = $survey->questions()->get();

            foreach ($questions as $question) {
                $questionRules = [];

                if ($question->is_required) {
                    $questionRules[] = 'required';
                } else {
                    $questionRules[] = 'nullable';
                }

                // Add type-specific validation
                switch ($question->type) {
                    case Question::TYPE_TEXT:
                        $questionRules[] = 'string';
                        $questionRules[] = 'max:1000';
                        break;

                    case Question::TYPE_TEXTAREA:
                        $questionRules[] = 'string';
                        $questionRules[] = 'max:5000';
                        break;

                    case Question::TYPE_RATING:
                        $questionRules[] = 'integer';
                        $questionRules[] = 'min:1';
                        $questionRules[] = 'max:5';
                        break;

                    case Question::TYPE_CHECKBOX:
                        $questionRules[] = 'array';
                        break;

                    case Question::TYPE_RADIO:
                    case Question::TYPE_SELECT:
                        $questionRules[] = 'string';
                        break;
                }

                $rules["answers.{$question->id}"] = $questionRules;
            }
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'answers.*.required' => 'This field is required.',
            'answers.*.string' => 'Please provide a valid text response.',
            'answers.*.integer' => 'Please select a rating.',
            'answers.*.min' => 'Please select a rating between 1 and 5.',
            'answers.*.max' => 'Please select a rating between 1 and 5.',
            'answers.*.array' => 'Please select at least one option.',
        ];
    }
}
