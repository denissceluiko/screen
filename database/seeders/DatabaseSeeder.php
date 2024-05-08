<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Screen;
use App\Models\Slide;
use App\Models\SlideShow;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = \App\Models\User::factory()->create([
            'name' => 'Dencel',
            'email' => 'admin@celuiko.com',
            'password' => bcrypt('changeme'),
            'role' => 'admin',
        ]);

        $team = $user->teams()->create([
            'name' => 'Test',
            'slug' => 'test',
        ]);

        $show = SlideShow::factory()
            ->recycle($team)
            ->create([
                'name' => 'Default',
                'settings' => [
                    'switchInterval' => 5,
                ],
            ]);

        Screen::factory()
            ->recycle($team)
            ->create([
                'slide_show_id' => $show->id,
            ]);
    }
}
