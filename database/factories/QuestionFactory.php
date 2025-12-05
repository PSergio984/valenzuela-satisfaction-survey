<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\Survey;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Question>
 */
class QuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'survey_id' => Survey::factory(),
            'type' => fake()->randomElement(array_keys(Question::TYPES)),
            'question' => fake()->sentence() . '?',
            'description' => fake()->optional()->sentence(),
            'is_required' => fake()->boolean(60),
            'order' => fake()->numberBetween(0, 100),
            'settings' => null,
        ];
    }

    public function text(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => Question::TYPE_TEXT,
        ]);
    }

    public function textarea(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => Question::TYPE_TEXTAREA,
        ]);
    }

    public function radio(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => Question::TYPE_RADIO,
        ]);
    }

    public function checkbox(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => Question::TYPE_CHECKBOX,
        ]);
    }

    public function select(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => Question::TYPE_SELECT,
        ]);
    }

    public function rating(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => Question::TYPE_RATING,
            'settings' => [
                'min' => 1,
                'max' => 5,
                'min_label' => 'Poor',
                'max_label' => 'Excellent',
            ],
        ]);
    }

    public function required(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_required' => true,
        ]);
    }
}
