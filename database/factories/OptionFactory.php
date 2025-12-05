<?php

namespace Database\Factories;

use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Option>
 */
class OptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $label = fake()->word();

        return [
            'question_id' => Question::factory(),
            'label' => ucfirst($label),
            'value' => $label,
            'order' => fake()->numberBetween(0, 10),
        ];
    }
}
