<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Survey>
 */
class SurveyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(4);

        return [
            'title' => $title,
            'description' => fake()->optional()->paragraph(),
            'slug' => Str::slug($title) . '-' . Str::random(6),
            'is_active' => fake()->boolean(80),
            'requires_authentication' => fake()->boolean(20),
            'collect_respondent_info' => fake()->boolean(70),
            'starts_at' => fake()->optional()->dateTimeBetween('-1 week', '+1 week'),
            'ends_at' => fake()->optional()->dateTimeBetween('+1 week', '+1 month'),
            'thank_you_message' => fake()->optional()->sentence(),
            'created_by' => User::factory(),
        ];
    }

    public function active(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => false,
        ]);
    }
}
