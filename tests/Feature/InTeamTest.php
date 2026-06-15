<?php

namespace Tests\Feature;

use App\Models\Slide;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class InTeamTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_without_team_id_throws_when_no_tenant(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/active tenant context/');

        Slide::create([
            'name' => 'Test',
            'type' => 'image',
            'path' => 'test.jpg',
            'original_path' => 'test.jpg',
            'original_name' => 'test.jpg',
            'token' => Str::random(32),
        ]);
    }

    public function test_creating_with_explicit_team_id_bypasses_tenant(): void
    {
        $team = Team::factory()->create();

        // team_id is not in $fillable (intentionally — InTeam sets it from the tenant).
        // forceFill bypasses mass-assignment so we can set it directly in tests.
        $slide = (new Slide)->forceFill([
            'team_id' => $team->id,
            'name' => 'Test',
            'type' => 'image',
            'path' => 'test.jpg',
            'original_path' => 'test.jpg',
            'original_name' => 'test.jpg',
            'token' => Str::random(32),
        ]);
        $slide->save();

        $this->assertEquals($team->id, $slide->team_id);
    }

    public function test_team_relationship_resolves(): void
    {
        $slide = Slide::factory()->create();

        $this->assertInstanceOf(Team::class, $slide->team);
        $this->assertEquals($slide->team_id, $slide->team->id);
    }
}
