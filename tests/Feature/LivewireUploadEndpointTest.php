<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LivewireUploadEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_request_is_rejected(): void
    {
        $response = $this->postJson('/livewire/upload-file');

        // postJson sends Accept: application/json so the auth middleware returns 401
        $response->assertUnauthorized();
    }

    public function test_authenticated_request_passes_auth_check(): void
    {
        $user = User::factory()->create();

        // Authenticated but no signed URL — Livewire rejects with 401, not a login redirect.
        // This confirms auth was satisfied but the signature check caught the bare request.
        $response = $this->actingAs($user)->postJson('/livewire/upload-file');

        $response->assertUnauthorized();
    }

    public function test_unauthenticated_browser_request_redirects_to_login(): void
    {
        $response = $this->post('/livewire/upload-file');

        $response->assertRedirectToRoute('filament.app.auth.login');
    }
}
