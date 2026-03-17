<?php

namespace Tests\Feature;

use App\Livewire\ShowScreen;
use App\Models\Screen;
use App\Models\Slide;
use App\Models\SlideShow;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SlideOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_screen_returns_slides_in_sort_order(): void
    {
        $team = Team::factory()->create();
        $slideShow = SlideShow::factory()->create(['team_id' => $team->id]);
        $screen = Screen::factory()->create(['team_id' => $team->id, 'slide_show_id' => $slideShow->id]);

        $first = Slide::factory()->create(['team_id' => $team->id]);
        $second = Slide::factory()->create(['team_id' => $team->id]);
        $third = Slide::factory()->create(['team_id' => $team->id]);

        // Attach in reverse order so natural insertion order would give wrong result
        $slideShow->slides()->attach($third->id, ['sort_order' => 1]);
        $slideShow->slides()->attach($first->id, ['sort_order' => 2]);
        $slideShow->slides()->attach($second->id, ['sort_order' => 3]);

        $component = Livewire::test(ShowScreen::class, ['screen' => $screen]);

        $slideIds = array_column($component->get('slides'), 'id');

        $this->assertEquals([$third->id, $first->id, $second->id], $slideIds);
    }

    public function test_reorder_table_updates_pivot_sort_order(): void
    {
        $team = Team::factory()->create();
        $slideShow = SlideShow::factory()->create(['team_id' => $team->id]);

        $first = Slide::factory()->create(['team_id' => $team->id]);
        $second = Slide::factory()->create(['team_id' => $team->id]);

        $slideShow->slides()->attach($first->id, ['sort_order' => 1]);
        $slideShow->slides()->attach($second->id, ['sort_order' => 2]);

        // Simulate what reorderTable does: reverse the order
        foreach ([$second->id, $first->id] as $position => $slideId) {
            $slideShow->slides()->updateExistingPivot($slideId, ['sort_order' => $position + 1]);
        }

        $slideShow->refresh();
        $ordered = $slideShow->slides()->get();

        $this->assertEquals($second->id, $ordered->first()->id);
        $this->assertEquals($first->id, $ordered->last()->id);
    }
}
