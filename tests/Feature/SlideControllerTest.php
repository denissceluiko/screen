<?php

namespace Tests\Feature;

use App\Models\Slide;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SlideControllerTest extends TestCase
{
    use RefreshDatabase;

    private function makeSlide(): Slide
    {
        $slide = Slide::factory()->create();
        Storage::disk('slides')->put($slide->path, 'fake-image-data');

        return $slide;
    }

    public function test_valid_token_returns_200_with_cache_headers(): void
    {
        Storage::fake('slides');
        $slide = $this->makeSlide();

        $response = $this->get(route('slide.show', $slide->token));

        $response->assertOk();
        // Symfony may reorder Cache-Control directives, so check for each directive individually
        $cacheControl = $response->headers->get('Cache-Control');
        $this->assertStringContainsString('public', $cacheControl);
        $this->assertStringContainsString('max-age=86400', $cacheControl);
        $this->assertNotEmpty($response->headers->get('ETag'));
        $this->assertNotEmpty($response->headers->get('Last-Modified'));
    }

    public function test_invalid_token_returns_404(): void
    {
        $response = $this->get(route('slide.show', 'this-token-does-not-exist'));

        $response->assertNotFound();
    }

    public function test_matching_etag_returns_304(): void
    {
        Storage::fake('slides');
        $slide = $this->makeSlide();

        $first = $this->get(route('slide.show', $slide->token));
        $etag = $first->headers->get('ETag');

        $response = $this->get(route('slide.show', $slide->token), ['If-None-Match' => $etag]);

        $response->assertStatus(304);
    }

    public function test_stale_etag_returns_200(): void
    {
        Storage::fake('slides');
        $slide = $this->makeSlide();

        $response = $this->get(route('slide.show', $slide->token), [
            'If-None-Match' => '"stale-etag-that-will-not-match"',
        ]);

        $response->assertOk();
    }

    public function test_etag_changes_after_slide_is_updated(): void
    {
        Storage::fake('slides');
        $this->freezeTime();

        $slide = $this->makeSlide();
        $first = $this->get(route('slide.show', $slide->token));

        // Travel forward so touch() produces a different updated_at timestamp
        $this->travel(5)->seconds();
        $slide->touch();

        $second = $this->get(route('slide.show', $slide->token));

        $this->assertNotEquals(
            $first->headers->get('ETag'),
            $second->headers->get('ETag'),
        );
    }
}
