<?php

namespace Database\Seeders;

use App\Models\Option;
use App\Models\Question;
use App\Models\Survey;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SurveySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::whereHas('roles', fn ($q) => $q->where('name', 'admin'))
            ->first() ?? User::first();

        $surveys = $this->getSurveyDefinitions();

        foreach ($surveys as $surveyData) {
            $survey = Survey::firstOrCreate(
                ['slug' => Str::slug($surveyData['title']).'-'.Str::random(6)],
                [
                    'title' => $surveyData['title'],
                    'description' => $surveyData['description'],
                    'is_active' => $surveyData['is_active'],
                    'collect_respondent_info' => $surveyData['collect_respondent_info'] ?? true,
                    'starts_at' => $surveyData['starts_at'] ?? null,
                    'ends_at' => $surveyData['ends_at'] ?? null,
                    'thank_you_message' => $surveyData['thank_you_message'],
                    'created_by' => $admin?->id,
                ]
            );

            if ($survey->wasRecentlyCreated && isset($surveyData['questions'])) {
                foreach ($surveyData['questions'] as $order => $questionData) {
                    $question = Question::create([
                        'survey_id' => $survey->id,
                        'type' => $questionData['type'],
                        'question' => $questionData['question'],
                        'description' => $questionData['description'] ?? null,
                        'is_required' => $questionData['is_required'] ?? true,
                        'order' => $order + 1,
                        'settings' => $questionData['settings'] ?? null,
                    ]);

                    if (isset($questionData['options'])) {
                        foreach ($questionData['options'] as $optionOrder => $optionLabel) {
                            Option::create([
                                'question_id' => $question->id,
                                'label' => $optionLabel,
                                'order' => $optionOrder,
                            ]);
                        }
                    }
                }
            }
        }

        $this->command->info('Surveys seeded successfully!');
        $this->command->info('Created '.count($surveys).' surveys with questions.');
    }

    /**
     * Get survey definitions with questions.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function getSurveyDefinitions(): array
    {
        return [
            // Survey 1: Customer Satisfaction (Main survey - lots of responses)
            [
                'title' => 'Customer Satisfaction Survey',
                'description' => 'Help us improve our services by sharing your experience. Your feedback is valuable to us.',
                'is_active' => true,
                'collect_respondent_info' => true,
                'thank_you_message' => 'Thank you for your valuable feedback! We appreciate you taking the time to help us improve.',
                'questions' => [
                    [
                        'type' => Question::TYPE_RATING,
                        'question' => 'How satisfied are you with our overall service?',
                        'description' => 'Rate from 1 (Very Dissatisfied) to 5 (Very Satisfied)',
                        'is_required' => true,
                        'settings' => ['min' => 1, 'max' => 5, 'min_label' => 'Very Dissatisfied', 'max_label' => 'Very Satisfied'],
                    ],
                    [
                        'type' => Question::TYPE_RATING,
                        'question' => 'How would you rate the quality of our products/services?',
                        'description' => 'Rate from 1 (Poor) to 5 (Excellent)',
                        'is_required' => true,
                        'settings' => ['min' => 1, 'max' => 5, 'min_label' => 'Poor', 'max_label' => 'Excellent'],
                    ],
                    [
                        'type' => Question::TYPE_RATING,
                        'question' => 'How satisfied are you with our customer support?',
                        'is_required' => true,
                        'settings' => ['min' => 1, 'max' => 5, 'min_label' => 'Very Dissatisfied', 'max_label' => 'Very Satisfied'],
                    ],
                    [
                        'type' => Question::TYPE_RADIO,
                        'question' => 'How did you hear about us?',
                        'is_required' => true,
                        'options' => ['Social Media', 'Friend/Family Referral', 'Online Search', 'Advertisement', 'Government Website', 'Other'],
                    ],
                    [
                        'type' => Question::TYPE_CHECKBOX,
                        'question' => 'Which services have you used?',
                        'description' => 'Select all that apply',
                        'is_required' => false,
                        'options' => ['Business Registration', 'Permit Applications', 'Tax Services', 'Document Requests', 'Consultation', 'Other'],
                    ],
                    [
                        'type' => Question::TYPE_SELECT,
                        'question' => 'How likely are you to recommend our services to others?',
                        'is_required' => true,
                        'options' => ['Very Unlikely', 'Unlikely', 'Neutral', 'Likely', 'Very Likely'],
                    ],
                    [
                        'type' => Question::TYPE_TEXTAREA,
                        'question' => 'What can we do to improve your experience?',
                        'description' => 'Please share any suggestions or comments.',
                        'is_required' => false,
                    ],
                ],
            ],

            // Survey 2: Government Services Feedback
            [
                'title' => 'Government Services Feedback',
                'description' => 'Rate your experience with our government services.',
                'is_active' => true,
                'collect_respondent_info' => true,
                'thank_you_message' => 'Thank you for your feedback! Your input helps us serve you better.',
                'questions' => [
                    [
                        'type' => Question::TYPE_RATING,
                        'question' => 'How would you rate the efficiency of our service delivery?',
                        'is_required' => true,
                        'settings' => ['min' => 1, 'max' => 5, 'min_label' => 'Very Slow', 'max_label' => 'Very Fast'],
                    ],
                    [
                        'type' => Question::TYPE_RATING,
                        'question' => 'How would you rate the courtesy of our staff?',
                        'is_required' => true,
                        'settings' => ['min' => 1, 'max' => 5, 'min_label' => 'Very Rude', 'max_label' => 'Very Courteous'],
                    ],
                    [
                        'type' => Question::TYPE_RATING,
                        'question' => 'How would you rate our facilities and cleanliness?',
                        'is_required' => true,
                        'settings' => ['min' => 1, 'max' => 5, 'min_label' => 'Very Poor', 'max_label' => 'Excellent'],
                    ],
                    [
                        'type' => Question::TYPE_RADIO,
                        'question' => 'Which department did you visit?',
                        'is_required' => true,
                        'options' => ['Administration', 'Finance', 'Operations', 'Customer Service', 'Information Technology', 'Other'],
                    ],
                    [
                        'type' => Question::TYPE_SELECT,
                        'question' => 'How long did you wait for service?',
                        'is_required' => true,
                        'options' => ['Less than 15 minutes', '15-30 minutes', '30-60 minutes', '1-2 hours', 'More than 2 hours'],
                    ],
                    [
                        'type' => Question::TYPE_TEXT,
                        'question' => 'What specific service did you avail?',
                        'is_required' => false,
                    ],
                    [
                        'type' => Question::TYPE_TEXTAREA,
                        'question' => 'Any additional comments or suggestions?',
                        'is_required' => false,
                    ],
                ],
            ],

            // Survey 3: Employee Engagement Survey
            [
                'title' => 'Employee Engagement Survey',
                'description' => 'Help us understand how we can make the workplace better for everyone.',
                'is_active' => true,
                'collect_respondent_info' => false,
                'thank_you_message' => 'Thank you for your honest feedback. Your responses are anonymous and will help improve our workplace.',
                'questions' => [
                    [
                        'type' => Question::TYPE_RATING,
                        'question' => 'How satisfied are you with your current role?',
                        'is_required' => true,
                        'settings' => ['min' => 1, 'max' => 5, 'min_label' => 'Very Dissatisfied', 'max_label' => 'Very Satisfied'],
                    ],
                    [
                        'type' => Question::TYPE_RATING,
                        'question' => 'How would you rate work-life balance in your department?',
                        'is_required' => true,
                        'settings' => ['min' => 1, 'max' => 5, 'min_label' => 'Very Poor', 'max_label' => 'Excellent'],
                    ],
                    [
                        'type' => Question::TYPE_RATING,
                        'question' => 'How supported do you feel by your immediate supervisor?',
                        'is_required' => true,
                        'settings' => ['min' => 1, 'max' => 5, 'min_label' => 'Not Supported', 'max_label' => 'Very Supported'],
                    ],
                    [
                        'type' => Question::TYPE_RADIO,
                        'question' => 'How long have you been with the organization?',
                        'is_required' => true,
                        'options' => ['Less than 1 year', '1-3 years', '3-5 years', '5-10 years', 'More than 10 years'],
                    ],
                    [
                        'type' => Question::TYPE_CHECKBOX,
                        'question' => 'What benefits do you value most?',
                        'is_required' => false,
                        'options' => ['Health Insurance', 'Retirement Benefits', 'Training Opportunities', 'Flexible Schedule', 'Leave Benefits', 'Performance Bonus'],
                    ],
                    [
                        'type' => Question::TYPE_TEXTAREA,
                        'question' => 'What improvements would you suggest for our workplace?',
                        'is_required' => false,
                    ],
                ],
            ],

            // Survey 4: Event Feedback (Completed/Past)
            [
                'title' => 'Community Event Feedback - Annual Celebration 2024',
                'description' => 'Share your experience at our recent community celebration.',
                'is_active' => false,
                'starts_at' => now()->subDays(45),
                'ends_at' => now()->subDays(30),
                'collect_respondent_info' => true,
                'thank_you_message' => 'Thank you for celebrating with us! See you at our next event.',
                'questions' => [
                    [
                        'type' => Question::TYPE_RATING,
                        'question' => 'How would you rate the overall event?',
                        'is_required' => true,
                        'settings' => ['min' => 1, 'max' => 5, 'min_label' => 'Poor', 'max_label' => 'Excellent'],
                    ],
                    [
                        'type' => Question::TYPE_RATING,
                        'question' => 'How would you rate the event organization?',
                        'is_required' => true,
                        'settings' => ['min' => 1, 'max' => 5, 'min_label' => 'Disorganized', 'max_label' => 'Well Organized'],
                    ],
                    [
                        'type' => Question::TYPE_CHECKBOX,
                        'question' => 'Which activities did you enjoy?',
                        'is_required' => false,
                        'options' => ['Cultural Shows', 'Food Festival', 'Sports Events', 'Art Exhibits', 'Musical Performances', 'Fireworks Display'],
                    ],
                    [
                        'type' => Question::TYPE_SELECT,
                        'question' => 'Would you attend similar events in the future?',
                        'is_required' => true,
                        'options' => ['Definitely Yes', 'Probably Yes', 'Not Sure', 'Probably No', 'Definitely No'],
                    ],
                ],
            ],

            // Survey 5: Website Usability
            [
                'title' => 'Website Usability Survey',
                'description' => 'Help us improve our online services by rating your website experience.',
                'is_active' => true,
                'collect_respondent_info' => false,
                'thank_you_message' => 'Thank you for helping us improve our digital services!',
                'questions' => [
                    [
                        'type' => Question::TYPE_RATING,
                        'question' => 'How easy was it to navigate our website?',
                        'is_required' => true,
                        'settings' => ['min' => 1, 'max' => 5, 'min_label' => 'Very Difficult', 'max_label' => 'Very Easy'],
                    ],
                    [
                        'type' => Question::TYPE_RATING,
                        'question' => 'How would you rate the loading speed of our website?',
                        'is_required' => true,
                        'settings' => ['min' => 1, 'max' => 5, 'min_label' => 'Very Slow', 'max_label' => 'Very Fast'],
                    ],
                    [
                        'type' => Question::TYPE_RATING,
                        'question' => 'How easy was it to find the information you needed?',
                        'is_required' => true,
                        'settings' => ['min' => 1, 'max' => 5, 'min_label' => 'Very Difficult', 'max_label' => 'Very Easy'],
                    ],
                    [
                        'type' => Question::TYPE_RADIO,
                        'question' => 'What device did you use to access our website?',
                        'is_required' => true,
                        'options' => ['Desktop Computer', 'Laptop', 'Tablet', 'Mobile Phone'],
                    ],
                    [
                        'type' => Question::TYPE_CHECKBOX,
                        'question' => 'What features would you like to see improved?',
                        'is_required' => false,
                        'options' => ['Search Functionality', 'Mobile Experience', 'Online Forms', 'Payment System', 'Document Downloads', 'Live Chat Support'],
                    ],
                    [
                        'type' => Question::TYPE_TEXTAREA,
                        'question' => 'Any other feedback about our website?',
                        'is_required' => false,
                    ],
                ],
            ],

            // Survey 6: Training Evaluation
            [
                'title' => 'Training Program Evaluation',
                'description' => 'Please provide feedback on the training session you attended.',
                'is_active' => true,
                'collect_respondent_info' => true,
                'thank_you_message' => 'Thank you for your feedback! Your input helps us improve our training programs.',
                'questions' => [
                    [
                        'type' => Question::TYPE_RATING,
                        'question' => 'How would you rate the overall quality of the training?',
                        'is_required' => true,
                        'settings' => ['min' => 1, 'max' => 5, 'min_label' => 'Poor', 'max_label' => 'Excellent'],
                    ],
                    [
                        'type' => Question::TYPE_RATING,
                        'question' => "How would you rate the trainer's knowledge and presentation skills?",
                        'is_required' => true,
                        'settings' => ['min' => 1, 'max' => 5, 'min_label' => 'Poor', 'max_label' => 'Excellent'],
                    ],
                    [
                        'type' => Question::TYPE_RATING,
                        'question' => 'How relevant was the content to your work?',
                        'is_required' => true,
                        'settings' => ['min' => 1, 'max' => 5, 'min_label' => 'Not Relevant', 'max_label' => 'Very Relevant'],
                    ],
                    [
                        'type' => Question::TYPE_SELECT,
                        'question' => 'Which training program did you attend?',
                        'is_required' => true,
                        'options' => ['Leadership Development', 'Technical Skills', 'Customer Service', 'IT and Digital Tools', 'Health and Safety', 'Other'],
                    ],
                    [
                        'type' => Question::TYPE_TEXTAREA,
                        'question' => 'What topics would you like covered in future trainings?',
                        'is_required' => false,
                    ],
                ],
            ],
        ];
    }
}
