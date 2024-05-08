<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Screen;
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

        Screen::factory()
            ->recycle($team)
            ->create();
    }
}
