<?php

namespace Database\Factories;

use App\Enums\SlideStatus;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Slide>
 */
class SlideFactory extends Factory
{
    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'name' => $this->faker->word(),
            'type' => 'image',
            'path' => $this->faker->uuid().'.jpg',
            'original_path' => $this->faker->uuid().'.jpg',
            'original_name' => $this->faker->word().'.jpg',
            'token' => Str::random(32),
            'status' => SlideStatus::Clean,
        ];
    }
}
