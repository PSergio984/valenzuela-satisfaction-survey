<?php

namespace Tests\Feature\Filament;

use App\Filament\Admin\Resources\Surveys\SurveyResource;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Response;
use App\Models\Survey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SurveyResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create super_admin role
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        $admin = User::factory()->create();
        $admin->assignRole('super_admin');
        $this->actingAs($admin);
    }

    public function test_get_eloquent_query_includes_average_rating_without_errors()
    {
        $survey = Survey::factory()->create();
        $question = Question::factory()->create([
            'survey_id' => $survey->id,
            'type' => Question::TYPE_RATING,
        ]);
        $response = Response::factory()->create(['survey_id' => $survey->id]);
        Answer::factory()->create([
            'response_id' => $response->id,
            'question_id' => $question->id,
            'value' => '5',
        ]);

        $query = SurveyResource::getEloquentQuery();
        $results = $query->get();

        $this->assertCount(1, $results);
        $this->assertEquals(5.0, (float) $results->first()->answers_avg_value);
    }
}
