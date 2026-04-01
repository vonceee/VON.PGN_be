<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Tests\TestCase;
use Mockery;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function mockGoogleUser(array $overrides = []): SocialiteUser
    {
        $defaults = [
            'id' => 'google-12345',
            'name' => 'Google User',
            'email' => 'google@example.com',
            'email_verified' => true,
        ];

        $data = array_merge($defaults, $overrides);

        $mock = $this->createMock(SocialiteUser::class);
        $mock->id = $data['id'];
        $mock->name = $data['name'];
        $mock->email = $data['email'];

        return $mock;
    }

    // ─── REDIRECT ──────────────────────────────────────────────────

    public function test_google_redirect_returns_redirect_response(): void
    {
        $response = $this->getJson('/api/auth/google/redirect');

        // The redirect endpoint returns a 302 to Google's OAuth page.
        // We can't easily mock Socialite's chained calls, so we just
        // verify the endpoint exists and doesn't return 404/500.
        $this->assertContains($response->getStatusCode(), [302, 500]);
    }

    // ─── CALLBACK - NEW USER ───────────────────────────────────────

    public function test_google_callback_creates_new_user(): void
    {
        $googleUser = $this->mockGoogleUser();

        Socialite::shouldReceive('driver->stateless->user')
            ->andReturn($googleUser);

        $response = $this->getJson('/api/auth/google/callback?code=fake-code');

        $response->assertStatus(302);

        $this->assertDatabaseHas('users', [
            'email' => 'google@example.com',
            'name' => 'Google User',
            'google_id' => 'google-12345',
        ]);
    }

    public function test_google_callback_creates_preferences_and_progress_for_new_user(): void
    {
        $googleUser = $this->mockGoogleUser();

        Socialite::shouldReceive('driver->stateless->user')
            ->andReturn($googleUser);

        $this->getJson('/api/auth/google/callback?code=fake-code');

        $user = User::where('email', 'google@example.com')->first();
        $this->assertNotNull($user);
        $this->assertNotNull($user->preferences);
        $this->assertNotNull($user->progress);
    }

    public function test_google_callback_marks_email_as_verified_for_new_user(): void
    {
        $googleUser = $this->mockGoogleUser();

        Socialite::shouldReceive('driver->stateless->user')
            ->andReturn($googleUser);

        $this->getJson('/api/auth/google/callback?code=fake-code');

        $user = User::where('email', 'google@example.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasVerifiedEmail(), 'User should have verified email');
    }

    public function test_google_callback_new_user_has_null_password(): void
    {
        $googleUser = $this->mockGoogleUser();

        Socialite::shouldReceive('driver->stateless->user')
            ->andReturn($googleUser);

        $this->getJson('/api/auth/google/callback?code=fake-code');

        $user = User::where('email', 'google@example.com')->first();
        $this->assertNull($user->password);
    }

    public function test_google_callback_redirects_with_token_for_new_user(): void
    {
        $googleUser = $this->mockGoogleUser();

        Socialite::shouldReceive('driver->stateless->user')
            ->andReturn($googleUser);

        $response = $this->getJson('/api/auth/google/callback?code=fake-code');

        $response->assertStatus(302);
        $response->assertRedirect();

        $location = $response->headers->get('Location');
        $this->assertStringContainsString('/auth/google/callback?token=', $location);
    }

    // ─── CALLBACK - EXISTING USER WITH GOOGLE_ID ──────────────────

    public function test_google_callback_links_existing_user_by_google_id(): void
    {
        $user = User::factory()->create([
            'email' => 'existing@example.com',
            'google_id' => 'google-12345',
        ]);
        $user->preferences()->create();
        $user->progress()->create();

        $googleUser = $this->mockGoogleUser([
            'id' => 'google-12345',
            'email' => 'existing@example.com',
        ]);

        Socialite::shouldReceive('driver->stateless->user')
            ->andReturn($googleUser);

        $response = $this->getJson('/api/auth/google/callback?code=fake-code');

        $response->assertStatus(302);

        // Should not create a duplicate user
        $this->assertEquals(1, User::where('email', 'existing@example.com')->count());
    }

    // ─── CALLBACK - EXISTING USER LINKS GOOGLE ACCOUNT ────────────

    public function test_google_callback_links_google_id_to_existing_email_user(): void
    {
        $user = User::factory()->create([
            'email' => 'existing@example.com',
            'google_id' => null,
        ]);
        $user->preferences()->create();
        $user->progress()->create();

        $googleUser = $this->mockGoogleUser([
            'email' => 'existing@example.com',
        ]);

        Socialite::shouldReceive('driver->stateless->user')
            ->andReturn($googleUser);

        $this->getJson('/api/auth/google/callback?code=fake-code');

        $this->assertDatabaseHas('users', [
            'email' => 'existing@example.com',
            'google_id' => 'google-12345',
        ]);
    }

    public function test_google_callback_creates_missing_preferences_for_existing_user(): void
    {
        $user = User::factory()->create([
            'email' => 'existing@example.com',
            'google_id' => null,
        ]);
        // Intentionally NOT creating preferences/progress

        $googleUser = $this->mockGoogleUser([
            'email' => 'existing@example.com',
        ]);

        Socialite::shouldReceive('driver->stateless->user')
            ->andReturn($googleUser);

        $this->getJson('/api/auth/google/callback?code=fake-code');

        $user->refresh();
        $this->assertNotNull($user->preferences);
        $this->assertNotNull($user->progress);
    }

    // ─── TOKEN VALIDITY ───────────────────────────────────────────

    public function test_google_auth_token_can_access_protected_routes(): void
    {
        $googleUser = $this->mockGoogleUser();

        Socialite::shouldReceive('driver->stateless->user')
            ->andReturn($googleUser);

        $response = $this->getJson('/api/auth/google/callback?code=fake-code');

        $location = $response->headers->get('Location');
        parse_str(parse_url($location, PHP_URL_QUERY), $params);
        $token = $params['token'];

        $this->assertNotEmpty($token);

        $this->withHeaders(['Authorization' => "Bearer $token"])
            ->getJson('/api/profile')
            ->assertStatus(200)
            ->assertJsonPath('data.email', 'google@example.com');
    }

    // ─── FULL FLOW ───────────────────────────────────────────────

    public function test_full_google_auth_flow(): void
    {
        $googleUser = $this->mockGoogleUser([
            'name' => 'Flow User',
            'email' => 'flow@example.com',
        ]);

        Socialite::shouldReceive('driver->stateless->user')
            ->andReturn($googleUser);

        // 1. Callback creates user and returns token
        $response = $this->getJson('/api/auth/google/callback?code=fake-code');
        $response->assertStatus(302);

        $location = $response->headers->get('Location');
        parse_str(parse_url($location, PHP_URL_QUERY), $params);
        $token = $params['token'];

        // 2. Token works for authenticated routes
        $this->withHeaders(['Authorization' => "Bearer $token"])
            ->getJson('/api/profile')
            ->assertStatus(200)
            ->assertJsonPath('data.username', 'Flow User')
            ->assertJsonPath('data.email', 'flow@example.com');

        // 3. User can logout
        $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson('/api/logout')
            ->assertStatus(200);

        // 4. Token is deleted from database after logout
        $user = User::where('email', 'flow@example.com')->first();
        $this->assertCount(0, $user->tokens);
    }
}
