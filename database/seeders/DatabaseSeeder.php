<?php

namespace Database\Seeders;

use App\Models\Option;
use App\Models\Question;
use App\Models\Survey;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create sample admin user for testing
        // TODO: Assign roles/permissions when role system is implemented
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'janrel motovlogs',
                'password' => Hash::make('Pwd@12345'),
                'email_verified_at' => now(),
            ]
        );

        // Create a sample customer satisfaction survey
        $survey = Survey::create([
            'title' => 'Customer Satisfaction Survey',
            'description' => 'Help us improve our services by answering a few questions.',
            'is_active' => true,
            'requires_authentication' => false,
            'collect_respondent_info' => true,
            'thank_you_message' => 'Thank you for your feedback! We appreciate your time.',
            'created_by' => $admin->id,
        ]);

        // Create questions
        $q1 = Question::create([
            'survey_id' => $survey->id,
            'type' => Question::TYPE_RATING,
            'question' => 'How satisfied are you with our service?',
            'description' => 'Rate your overall satisfaction.',
            'is_required' => true,
            'order' => 1,
            'settings' => [
                'min' => 1,
                'max' => 5,
                'min_label' => 'Very Dissatisfied',
                'max_label' => 'Very Satisfied',
            ],
        ]);

        $q2 = Question::create([
            'survey_id' => $survey->id,
            'type' => Question::TYPE_RADIO,
            'question' => 'How did you hear about us?',
            'is_required' => true,
            'order' => 2,
        ]);

        foreach (['Social Media', 'Friend/Family', 'Online Search', 'Advertisement', 'Other'] as $i => $option) {
            Option::create([
                'question_id' => $q2->id,
                'label' => $option,
                'order' => $i,
            ]);
        }

        $q3 = Question::create([
            'survey_id' => $survey->id,
            'type' => Question::TYPE_CHECKBOX,
            'question' => 'Which of our services have you used?',
            'description' => 'Select all that apply.',
            'is_required' => false,
            'order' => 3,
        ]);

        foreach (['Consultation', 'Installation', 'Maintenance', 'Support', 'Training'] as $i => $option) {
            Option::create([
                'question_id' => $q3->id,
                'label' => $option,
                'order' => $i,
            ]);
        }

        $q4 = Question::create([
            'survey_id' => $survey->id,
            'type' => Question::TYPE_TEXTAREA,
            'question' => 'What can we do to improve your experience?',
            'description' => 'Please share your suggestions.',
            'is_required' => false,
            'order' => 4,
        ]);

        $q5 = Question::create([
            'survey_id' => $survey->id,
            'type' => Question::TYPE_SELECT,
            'question' => 'How likely are you to recommend us?',
            'is_required' => true,
            'order' => 5,
        ]);

        foreach (['Very Unlikely', 'Unlikely', 'Neutral', 'Likely', 'Very Likely'] as $i => $option) {
            Option::create([
                'question_id' => $q5->id,
                'label' => $option,
                'order' => $i,
            ]);
        }
    }
}
