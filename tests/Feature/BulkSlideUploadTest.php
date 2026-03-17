<?php

namespace Tests\Feature;

use App\Filament\App\Resources\SlideResource\Pages\ListSlides;
use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class BulkSlideUploadTest extends TestCase
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

    /**
     * Put a minimal but valid JPEG on the fake slides disk and return its path.
     */
    private function fakeJpeg(string $filename = 'test.jpg'): string
    {
        $image = imagecreatetruecolor(1, 1);
        ob_start();
        imagejpeg($image);
        $content = ob_get_clean();

        Storage::disk('slides')->put($filename, $content);

        return $filename;
    }

    public function test_bulk_upload_creates_one_slide_per_file(): void
    {
        Storage::fake('slides');
        $team = $this->actingAsTeamMember();

        $file1 = $this->fakeJpeg('alpha.jpg');
        $file2 = $this->fakeJpeg('beta.jpg');

        Livewire::test(ListSlides::class)
            ->callAction('bulk_upload', data: [
                'files' => [$file1, $file2],
                'original_names' => ['alpha.jpg', 'beta.jpg'],
            ]);

        $this->assertDatabaseCount('slides', 2);
    }

    public function test_bulk_upload_names_slides_from_original_filename(): void
    {
        Storage::fake('slides');
        $team = $this->actingAsTeamMember();

        $file = $this->fakeJpeg('summer-campaign.jpg');

        Livewire::test(ListSlides::class)
            ->callAction('bulk_upload', data: [
                'files' => [$file],
                'original_names' => ['summer-campaign.jpg'],
            ]);

        $this->assertDatabaseHas('slides', ['name' => 'summer-campaign', 'team_id' => $team->id]);
    }

    public function test_bulk_upload_assigns_unique_tokens_to_each_slide(): void
    {
        Storage::fake('slides');
        $this->actingAsTeamMember();

        $file1 = $this->fakeJpeg('img1.jpg');
        $file2 = $this->fakeJpeg('img2.jpg');

        Livewire::test(ListSlides::class)
            ->callAction('bulk_upload', data: [
                'files' => [$file1, $file2],
                'original_names' => ['img1.jpg', 'img2.jpg'],
            ]);

        $tokens = \App\Models\Slide::pluck('token');

        $this->assertCount(2, $tokens);
        $this->assertCount(2, $tokens->unique());
    }
}
