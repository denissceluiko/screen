<?php

namespace Tests\Feature;

use App\Filament\App\Resources\SlideShowResource\Pages\ListSlideShows;
use App\Models\Slide;
use App\Models\SlideShow;
use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DuplicateSlideShowTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsTeamMember(): Team
    {
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $team->members()->attach($user);

        $this->actingAs($user);
        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($team);

        return $team;
    }

    public function test_duplicate_creates_copy_with_prefixed_name(): void
    {
        $team = $this->actingAsTeamMember();
        $original = SlideShow::factory()->create(['team_id' => $team->id, 'name' => 'Summer']);

        Livewire::test(ListSlideShows::class)
            ->callTableAction('duplicate', $original);

        $this->assertDatabaseHas('slide_shows', ['name' => 'Copy of Summer', 'team_id' => $team->id]);
    }

    public function test_duplicate_copies_settings(): void
    {
        $team = $this->actingAsTeamMember();
        $original = SlideShow::factory()->create([
            'team_id' => $team->id,
            'settings' => ['switchInterval' => '15'],
        ]);

        Livewire::test(ListSlideShows::class)
            ->callTableAction('duplicate', $original);

        $copy = SlideShow::where('team_id', $team->id)
            ->where('id', '!=', $original->id)
            ->sole();

        $this->assertEquals('15', $copy->settings['switchInterval']);
    }

    public function test_duplicate_attaches_same_slides_with_sort_order(): void
    {
        $team = $this->actingAsTeamMember();
        $original = SlideShow::factory()->create(['team_id' => $team->id]);

        $slideA = Slide::factory()->create(['team_id' => $team->id]);
        $slideB = Slide::factory()->create(['team_id' => $team->id]);
        $original->slides()->attach($slideA->id, ['sort_order' => 1]);
        $original->slides()->attach($slideB->id, ['sort_order' => 2]);

        Livewire::test(ListSlideShows::class)
            ->callTableAction('duplicate', $original);

        $copy = SlideShow::where('team_id', $team->id)
            ->where('id', '!=', $original->id)
            ->sole();

        $copySlides = $copy->slides()->get();
        $this->assertCount(2, $copySlides);
        $this->assertEquals($slideA->id, $copySlides->first()->id);
        $this->assertEquals(1, $copySlides->first()->pivot->sort_order);
        $this->assertEquals($slideB->id, $copySlides->last()->id);
        $this->assertEquals(2, $copySlides->last()->pivot->sort_order);
    }

    public function test_duplicate_does_not_modify_original(): void
    {
        $team = $this->actingAsTeamMember();
        $original = SlideShow::factory()->create(['team_id' => $team->id, 'name' => 'Original']);

        $slide = Slide::factory()->create(['team_id' => $team->id]);
        $original->slides()->attach($slide->id, ['sort_order' => 1]);

        Livewire::test(ListSlideShows::class)
            ->callTableAction('duplicate', $original);

        $this->assertDatabaseHas('slide_shows', ['id' => $original->id, 'name' => 'Original']);
        $this->assertCount(1, $original->fresh()->slides);
    }
}
