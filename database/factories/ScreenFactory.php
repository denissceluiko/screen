<?php

namespace Database\Factories;

use App\Models\Screen;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Screen>
 */
class ScreenFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'name' => $this->faker->words(2, true),
            'slug' => $this->faker->uuid(),
        ];
    }
}
