<?php

namespace Database\Factories;

use App\Models\Survey;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Response>
 */
class ResponseFactory extends Factory
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
            'user_id' => null,
            'respondent_name' => fake()->optional()->name(),
            'respondent_email' => fake()->optional()->safeEmail(),
            'respondent_phone' => fake()->optional()->phoneNumber(),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'submitted_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }

    public function authenticated(): static
    {
        return $this->state(fn(array $attributes) => [
            'user_id' => User::factory(),
        ]);
    }

    public function anonymous(): static
    {
        return $this->state(fn(array $attributes) => [
            'respondent_name' => null,
            'respondent_email' => null,
            'respondent_phone' => null,
        ]);
    }
}
