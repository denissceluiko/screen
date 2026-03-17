<?php

namespace Tests\Feature;

use App\Livewire\ShowScreen;
use App\Models\Screen;
use App\Models\Slide;
use App\Models\SlideShow;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ShowScreenTest extends TestCase
{
    use RefreshDatabase;

    public function test_screen_with_no_slideshow_has_empty_slides(): void
    {
        $screen = Screen::factory()->create(['slide_show_id' => null]);

        Livewire::test(ShowScreen::class, ['screen' => $screen])
            ->assertSet('slides', []);
    }

    public function test_update_is_noop_when_nothing_changed(): void
    {
        $screen = Screen::factory()->create();

        $component = Livewire::test(ShowScreen::class, ['screen' => $screen]);

        $hashBefore = $component->get('slidesHash');
        $slidesBefore = $component->get('slides');

        $component->call('update');

        $component->assertSet('slidesHash', $hashBefore);
        $this->assertEquals($slidesBefore, $component->get('slides'));
    }

    public function test_update_refreshes_when_slideshow_changes(): void
    {
        Storage::fake('slides');

        $team = Team::factory()->create();
        $slideShow = SlideShow::factory()->create(['team_id' => $team->id]);
        $screen = Screen::factory()->create(['team_id' => $team->id, 'slide_show_id' => $slideShow->id]);

        $component = Livewire::test(ShowScreen::class, ['screen' => $screen]);

        $this->assertEmpty($component->get('slides'));
        $hashBefore = $component->get('slidesHash');

        $slide = Slide::factory()->create(['team_id' => $team->id]);
        Storage::disk('slides')->put($slide->path, 'fake');
        $slideShow->slides()->attach($slide->id);
        $slideShow->touch();

        $component->call('update');

        $this->assertNotEquals($hashBefore, $component->get('slidesHash'));
        $this->assertCount(1, $component->get('slides'));
    }

    public function test_mount_records_last_seen_at(): void
    {
        $screen = Screen::factory()->create(['last_seen_at' => null]);

        Livewire::test(ShowScreen::class, ['screen' => $screen]);

        $this->assertNotNull($screen->fresh()->last_seen_at);
    }

    public function test_update_records_last_seen_at_even_when_hash_unchanged(): void
    {
        $screen = Screen::factory()->create(['last_seen_at' => null]);

        $component = Livewire::test(ShowScreen::class, ['screen' => $screen]);

        // Null last_seen_at after mount would fail — clear it manually to isolate update()
        $screen->forceFill(['last_seen_at' => null])->saveQuietly();

        $component->call('update');

        $this->assertNotNull($screen->fresh()->last_seen_at);
    }

    public function test_record_seen_does_not_bump_updated_at(): void
    {
        $this->freezeTime();

        $screen = Screen::factory()->create();
        $originalUpdatedAt = $screen->updated_at;

        $this->travel(5)->seconds();

        Livewire::test(ShowScreen::class, ['screen' => $screen]);

        $this->assertEquals($originalUpdatedAt, $screen->fresh()->updated_at);
    }

    public function test_slide_urls_use_token_route(): void
    {
        Storage::fake('slides');

        $team = Team::factory()->create();
        $slideShow = SlideShow::factory()->create(['team_id' => $team->id]);
        $slide = Slide::factory()->create(['team_id' => $team->id]);
        Storage::disk('slides')->put($slide->path, 'fake');
        $slideShow->slides()->attach($slide->id);
        $screen = Screen::factory()->create(['team_id' => $team->id, 'slide_show_id' => $slideShow->id]);

        $component = Livewire::test(ShowScreen::class, ['screen' => $screen]);

        $slides = $component->get('slides');
        $this->assertCount(1, $slides);
        $this->assertStringContainsString(
            route('slide.show', $slide->token),
            $slides[0]['path'],
        );
    }
}
