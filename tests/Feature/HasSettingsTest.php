<?php

namespace Tests\Feature;

use App\Models\Screen;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class HasSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_defaults_returned_when_settings_null(): void
    {
        $screen = Screen::factory()->create(['settings' => null]);

        $this->assertEquals('1920', $screen->settings['width']);
        $this->assertEquals('1080', $screen->settings['height']);
        $this->assertEquals('10', $screen->settings['updateInterval']);
    }

    public function test_stored_value_overrides_default(): void
    {
        // Pass an array — the HasSettings setter converts it to JSON
        $screen = Screen::factory()->create(['settings' => ['width' => '1280']]);

        $this->assertEquals('1280', $screen->settings['width']);
        $this->assertEquals('1080', $screen->settings['height']); // default still applied
    }

    public function test_all_defaults_returned_for_corrupt_json(): void
    {
        $screen = Screen::factory()->create();

        DB::table('screens')->where('id', $screen->id)->update(['settings' => 'not valid json {{{']);
        $screen->refresh();

        $this->assertEquals('1920', $screen->settings['width']);
        $this->assertEquals('1080', $screen->settings['height']);
        $this->assertEquals('10', $screen->settings['updateInterval']);
    }

    public function test_settings_written_and_read_back_correctly(): void
    {
        $screen = Screen::factory()->create();
        $screen->settings = ['width' => '2560', 'height' => '1440', 'updateInterval' => '30'];
        $screen->save();

        $screen->refresh();

        $this->assertEquals('2560', $screen->settings['width']);
        $this->assertEquals('1440', $screen->settings['height']);
        $this->assertEquals('30', $screen->settings['updateInterval']);
    }
}
