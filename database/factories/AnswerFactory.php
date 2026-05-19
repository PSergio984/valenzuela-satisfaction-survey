<?php

namespace Database\Factories;

use App\Models\Answer;
use App\Models\Question;
use App\Models\Response;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Answer>
 */
class AnswerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'response_id' => Response::factory(),
            'question_id' => Question::factory(),
            'value' => fake()->sentence(),
            'selected_options' => null,
        ];
    }

    public function withSelectedOptions(array $optionIds): static
    {
        return $this->state(fn (array $attributes) => [
            'value' => null,
            'selected_options' => $optionIds,
        ]);
    }
}
