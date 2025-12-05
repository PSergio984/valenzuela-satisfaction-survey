<?php

use App\Filament\Admin\Widgets\LatestResponsesWidget;
use App\Filament\Admin\Widgets\RatingsChart;
use App\Filament\Admin\Widgets\ResponsesChart;
use App\Filament\Admin\Widgets\SurveyStatsWidget;
use App\Models\Answer;
use App\Models\Option;
use App\Models\Question;
use App\Models\Response;
use App\Models\Survey;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\actingAs;

/**
 * Helper function to set up admin user and sample data.
 */
function setupDashboardTestData(): User
{
    // Create super_admin role if it doesn't exist
    $role = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

    // Create admin user
    $admin = User::factory()->create();
    $admin->assignRole('super_admin');

    // Create a survey with questions and responses
    $survey = Survey::factory()->create([
        'is_active' => true,
        'created_by' => $admin->id,
    ]);

    // Create rating question
    $ratingQuestion = Question::factory()->create([
        'survey_id' => $survey->id,
        'type' => Question::TYPE_RATING,
        'question' => 'How satisfied are you?',
        'is_required' => true,
        'order' => 1,
        'settings' => ['min' => 1, 'max' => 5],
    ]);

    // Create radio question with options
    $radioQuestion = Question::factory()->create([
        'survey_id' => $survey->id,
        'type' => Question::TYPE_RADIO,
        'question' => 'How did you hear about us?',
        'is_required' => true,
        'order' => 2,
    ]);

    foreach (['Social Media', 'Friend', 'Search', 'Other'] as $i => $label) {
        Option::factory()->create([
            'question_id' => $radioQuestion->id,
            'label' => $label,
            'order' => $i,
        ]);
    }

    // Create responses with answers spread across 30 days
    for ($i = 0; $i < 30; $i++) {
        $response = Response::factory()->create([
            'survey_id' => $survey->id,
            'submitted_at' => now()->subDays($i)->setTime(rand(8, 17), rand(0, 59)),
            'respondent_name' => fake()->name(),
            'respondent_email' => fake()->email(),
        ]);

        // Create rating answer
        Answer::factory()->create([
            'response_id' => $response->id,
            'question_id' => $ratingQuestion->id,
            'value' => (string) rand(3, 5), // Weighted towards positive
        ]);

        // Create radio answer
        $option = $radioQuestion->options->random();
        Answer::factory()->create([
            'response_id' => $response->id,
            'question_id' => $radioQuestion->id,
            'value' => $option->label,
            'selected_options' => [$option->id],
        ]);
    }

    // Set Filament panel context
    Filament::setCurrentPanel(Filament::getPanel('admin'));

    return $admin;
}

describe('SurveyStatsWidget', function () {
    it('displays total surveys count', function () {
        $admin = setupDashboardTestData();
        actingAs($admin);

        $surveyCount = Survey::count();

        Livewire::test(SurveyStatsWidget::class)
            ->assertSee('Total Surveys')
            ->assertSee((string) $surveyCount);
    });

    it('displays total responses count', function () {
        $admin = setupDashboardTestData();
        actingAs($admin);

        $totalResponses = Response::count();

        Livewire::test(SurveyStatsWidget::class)
            ->assertSee('Total Responses')
            ->assertSee((string) $totalResponses);
    });

    it('displays average rating stat', function () {
        $admin = setupDashboardTestData();
        actingAs($admin);

        Livewire::test(SurveyStatsWidget::class)
            ->assertSee('Average Rating');
    });

    it('displays weekly trend stat', function () {
        $admin = setupDashboardTestData();
        actingAs($admin);

        Livewire::test(SurveyStatsWidget::class)
            ->assertSee('Weekly Trend');
    });
});

describe('ResponsesChart', function () {
    it('renders with correct heading', function () {
        $admin = setupDashboardTestData();
        actingAs($admin);

        Livewire::test(ResponsesChart::class)
            ->assertSee('Responses Over Time');
    });

    it('renders successfully without errors', function () {
        $admin = setupDashboardTestData();
        actingAs($admin);

        Livewire::test(ResponsesChart::class)
            ->assertSuccessful();
    });
});

describe('RatingsChart', function () {
    it('renders with correct heading', function () {
        $admin = setupDashboardTestData();
        actingAs($admin);

        Livewire::test(RatingsChart::class)
            ->assertSee('Rating Distribution');
    });

    it('renders successfully without errors', function () {
        $admin = setupDashboardTestData();
        actingAs($admin);

        Livewire::test(RatingsChart::class)
            ->assertSuccessful();
    });
});

describe('LatestResponsesWidget', function () {
    it('renders the table widget', function () {
        $admin = setupDashboardTestData();
        actingAs($admin);

        Livewire::test(LatestResponsesWidget::class)
            ->assertSuccessful();
    });
});

describe('Dashboard Data Integrity', function () {
    it('creates surveys with questions', function () {
        setupDashboardTestData();

        $surveys = Survey::withCount('questions')->get();
        expect($surveys)->not->toBeEmpty();

        foreach ($surveys as $survey) {
            expect($survey->questions_count)->toBeGreaterThan(0);
        }
    });

    it('creates responses with answers', function () {
        setupDashboardTestData();

        $responses = Response::withCount('answers')->get();
        expect($responses)->not->toBeEmpty();

        foreach ($responses as $response) {
            expect($response->answers_count)->toBeGreaterThan(0);
        }
    });

    it('rating answers have valid values between 1 and 5', function () {
        setupDashboardTestData();

        $ratingAnswers = Answer::join('questions', 'answers.question_id', '=', 'questions.id')
            ->where('questions.type', Question::TYPE_RATING)
            ->whereNotNull('answers.value')
            ->select('answers.*')
            ->get();

        expect($ratingAnswers)->not->toBeEmpty();

        foreach ($ratingAnswers as $answer) {
            expect((int) $answer->value)->toBeGreaterThanOrEqual(1);
            expect((int) $answer->value)->toBeLessThanOrEqual(5);
        }
    });

    it('responses are distributed across multiple days', function () {
        setupDashboardTestData();

        $daysWithResponses = Response::selectRaw('DATE(submitted_at) as date')
            ->distinct()
            ->count();

        expect($daysWithResponses)->toBeGreaterThan(1);
    });

    it('calculates average rating within valid range', function () {
        setupDashboardTestData();

        $avgRating = Answer::join('questions', 'answers.question_id', '=', 'questions.id')
            ->where('questions.type', Question::TYPE_RATING)
            ->whereNotNull('answers.value')
            ->avg('answers.value');

        expect($avgRating)->toBeGreaterThanOrEqual(1);
        expect($avgRating)->toBeLessThanOrEqual(5);
    });
});
