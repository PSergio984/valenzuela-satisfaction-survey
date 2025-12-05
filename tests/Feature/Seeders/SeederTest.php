<?php

use App\Models\Answer;
use App\Models\Question;
use App\Models\Response;
use App\Models\Survey;
use App\Models\User;
use Database\Seeders\ResponseSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\SurveySeeder;
use Database\Seeders\UserSeeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

describe('RolesAndPermissionsSeeder', function () {
    it('creates the expected roles', function () {
        $this->seed(RolesAndPermissionsSeeder::class);

        expect(Role::where('name', 'super_admin')->exists())->toBeTrue();
        expect(Role::where('name', 'admin')->exists())->toBeTrue();
        expect(Role::where('name', 'staff')->exists())->toBeTrue();
    });

    it('creates permissions for each resource', function () {
        $this->seed(RolesAndPermissionsSeeder::class);

        $permissions = Permission::pluck('name')->toArray();

        // Check survey permissions
        expect($permissions)->toContain('view_surveys');
        expect($permissions)->toContain('create_surveys');
        expect($permissions)->toContain('edit_surveys');
        expect($permissions)->toContain('delete_surveys');

        // Check user permissions
        expect($permissions)->toContain('view_users');
        expect($permissions)->toContain('create_users');
        expect($permissions)->toContain('edit_users');
        expect($permissions)->toContain('delete_users');
    });

    it('assigns permissions to admin role', function () {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = Role::where('name', 'admin')->first();

        expect($admin->hasPermissionTo('view_surveys'))->toBeTrue();
        expect($admin->hasPermissionTo('create_surveys'))->toBeTrue();
        expect($admin->hasPermissionTo('view_users'))->toBeTrue();
    });

    it('assigns limited permissions to staff role', function () {
        $this->seed(RolesAndPermissionsSeeder::class);

        $staff = Role::where('name', 'staff')->first();

        expect($staff->hasPermissionTo('view_surveys'))->toBeTrue();
        expect($staff->hasPermissionTo('view_responses'))->toBeTrue();
        expect($staff->hasPermissionTo('create_surveys'))->toBeFalse();
    });
});

describe('UserSeeder', function () {
    beforeEach(function () {
        $this->seed(RolesAndPermissionsSeeder::class);
    });

    it('creates super admin user', function () {
        $this->seed(UserSeeder::class);

        $superAdmin = User::where('email', 'superadmin@gmail.com')->first();

        expect($superAdmin)->not->toBeNull();
        expect($superAdmin->hasRole('super_admin'))->toBeTrue();
    });

    it('creates admin users', function () {
        $this->seed(UserSeeder::class);

        $admin = User::where('email', 'admin@valenzuela.gov.ph')->first();

        expect($admin)->not->toBeNull();
        expect($admin->hasRole('admin'))->toBeTrue();
    });

    it('creates staff users', function () {
        $this->seed(UserSeeder::class);

        $staff = User::where('email', 'staff1@valenzuela.gov.ph')->first();

        expect($staff)->not->toBeNull();
        expect($staff->hasRole('staff'))->toBeTrue();
    });

    it('creates users without roles', function () {
        $this->seed(UserSeeder::class);

        $regularUser = User::where('email', 'demo@example.com')->first();

        expect($regularUser)->not->toBeNull();
        expect($regularUser->roles)->toBeEmpty();
    });

    it('creates exactly 8 users', function () {
        $this->seed(UserSeeder::class);

        expect(User::count())->toBe(8);
    });
});

describe('SurveySeeder', function () {
    beforeEach(function () {
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->seed(UserSeeder::class);
    });

    it('creates multiple surveys', function () {
        $this->seed(SurveySeeder::class);

        expect(Survey::count())->toBeGreaterThanOrEqual(6);
    });

    it('creates surveys with questions', function () {
        $this->seed(SurveySeeder::class);

        $surveys = Survey::withCount('questions')->get();

        foreach ($surveys as $survey) {
            expect($survey->questions_count)->toBeGreaterThan(0);
        }
    });

    it('creates questions with options where applicable', function () {
        $this->seed(SurveySeeder::class);

        $questionsWithOptions = Question::whereIn('type', [
            Question::TYPE_RADIO,
            Question::TYPE_SELECT,
            Question::TYPE_CHECKBOX,
        ])->withCount('options')->get();

        foreach ($questionsWithOptions as $question) {
            expect($question->options_count)->toBeGreaterThan(0);
        }
    });

    it('creates at least one active survey', function () {
        $this->seed(SurveySeeder::class);

        expect(Survey::where('is_active', true)->count())->toBeGreaterThan(0);
    });

    it('creates rating type questions', function () {
        $this->seed(SurveySeeder::class);

        expect(Question::where('type', Question::TYPE_RATING)->count())->toBeGreaterThan(0);
    });
});

describe('ResponseSeeder', function () {
    beforeEach(function () {
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->seed(UserSeeder::class);
        $this->seed(SurveySeeder::class);
    });

    it('creates responses for each survey', function () {
        $this->seed(ResponseSeeder::class);

        $surveysWithResponses = Survey::withCount('responses')->get();

        foreach ($surveysWithResponses as $survey) {
            expect($survey->responses_count)->toBeGreaterThan(0);
        }
    });

    it('creates answers for each response', function () {
        $this->seed(ResponseSeeder::class);

        $responses = Response::withCount('answers')->limit(20)->get();

        foreach ($responses as $response) {
            expect($response->answers_count)->toBeGreaterThan(0);
        }
    });

    it('creates responses distributed across multiple days', function () {
        $this->seed(ResponseSeeder::class);

        $daysWithResponses = Response::selectRaw('DATE(submitted_at) as date')
            ->distinct()
            ->count();

        expect($daysWithResponses)->toBeGreaterThan(30);
    });

    it('creates rating answers with valid values', function () {
        $this->seed(ResponseSeeder::class);

        $ratingAnswers = Answer::join('questions', 'answers.question_id', '=', 'questions.id')
            ->where('questions.type', Question::TYPE_RATING)
            ->whereNotNull('answers.value')
            ->select('answers.value')
            ->get();

        foreach ($ratingAnswers as $answer) {
            expect((int) $answer->value)->toBeGreaterThanOrEqual(1);
            expect((int) $answer->value)->toBeLessThanOrEqual(5);
        }
    });

    it('creates responses with respondent info for appropriate surveys', function () {
        $this->seed(ResponseSeeder::class);

        $surveysWithInfo = Survey::where('collect_respondent_info', true)->pluck('id');
        $responsesWithInfo = Response::whereIn('survey_id', $surveysWithInfo)
            ->whereNotNull('respondent_name')
            ->count();

        expect($responsesWithInfo)->toBeGreaterThan(0);
    });
});
