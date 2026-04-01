<?php

namespace Tests\Feature;

use App\Models\Tournament;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TournamentLinkTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('auth_token')->plainTextToken;
    }

    private function validTournamentData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Manila Open 2026',
            'status' => 'upcoming',
            'start_date' => '2026-06-01',
            'end_date' => '2026-06-05',
            'location' => 'SMX Convention Center',
            'format' => 'Swiss System',
            'time_control' => '90 min + 30 sec increment',
            'entry_fee' => '500',
            'organizer' => 'NCFP',
            'contact_email' => 'info@ncfp.ph',
            'registration_instructions' => 'Contact us to register',
            'rounds' => 9,
        ], $overrides);
    }

    // ─── STORE ─────────────────────────────────────────────────────

    public function test_can_create_tournament_with_link(): void
    {
        $response = $this->withHeaders(['Authorization' => "Bearer {$this->token}"])
            ->postJson('/api/my/tournaments', $this->validTournamentData([
                'link' => 'https://www.facebook.com/ManilaOpen/posts/12345',
            ]));

        $response->assertStatus(201)
            ->assertJsonPath('data.link', 'https://www.facebook.com/ManilaOpen/posts/12345');

        $this->assertDatabaseHas('tournaments', [
            'name' => 'Manila Open 2026',
            'link' => 'https://www.facebook.com/ManilaOpen/posts/12345',
        ]);
    }

    public function test_can_create_tournament_without_link(): void
    {
        $response = $this->withHeaders(['Authorization' => "Bearer {$this->token}"])
            ->postJson('/api/my/tournaments', $this->validTournamentData());

        $response->assertStatus(201)
            ->assertJsonPath('data.link', null);

        $this->assertDatabaseHas('tournaments', [
            'name' => 'Manila Open 2026',
            'link' => null,
        ]);
    }

    public function test_link_validation_rejects_invalid_url(): void
    {
        $response = $this->withHeaders(['Authorization' => "Bearer {$this->token}"])
            ->postJson('/api/my/tournaments', $this->validTournamentData([
                'link' => 'not-a-valid-url',
            ]));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['link']);
    }

    public function test_link_validation_rejects_non_url_string(): void
    {
        $response = $this->withHeaders(['Authorization' => "Bearer {$this->token}"])
            ->postJson('/api/my/tournaments', $this->validTournamentData([
                'link' => 'just some text',
            ]));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['link']);
    }

    // ─── UPDATE ────────────────────────────────────────────────────

    public function test_can_update_tournament_link(): void
    {
        $tournament = Tournament::factory()->create([
            'created_by' => $this->user->id,
            'link' => null,
        ]);

        $response = $this->withHeaders(['Authorization' => "Bearer {$this->token}"])
            ->putJson("/api/my/tournaments/{$tournament->slug}", [
                'link' => 'https://www.facebook.com/UpdatedPost/99999',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.link', 'https://www.facebook.com/UpdatedPost/99999');

        $this->assertDatabaseHas('tournaments', [
            'id' => $tournament->id,
            'link' => 'https://www.facebook.com/UpdatedPost/99999',
        ]);
    }

    public function test_can_clear_tournament_link(): void
    {
        $tournament = Tournament::factory()->create([
            'created_by' => $this->user->id,
            'link' => 'https://www.facebook.com/OldPost/123',
        ]);

        $response = $this->withHeaders(['Authorization' => "Bearer {$this->token}"])
            ->putJson("/api/my/tournaments/{$tournament->slug}", [
                'link' => null,
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.link', null);

        $this->assertDatabaseHas('tournaments', [
            'id' => $tournament->id,
            'link' => null,
        ]);
    }

    public function test_update_rejects_invalid_link_url(): void
    {
        $tournament = Tournament::factory()->create([
            'created_by' => $this->user->id,
        ]);

        $response = $this->withHeaders(['Authorization' => "Bearer {$this->token}"])
            ->putJson("/api/my/tournaments/{$tournament->slug}", [
                'link' => 'not-a-url',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['link']);
    }

    // ─── RETRIEVE ──────────────────────────────────────────────────

    public function test_public_tournament_endpoint_returns_link(): void
    {
        $tournament = Tournament::factory()->create([
            'link' => 'https://www.facebook.com/ChessPost/555',
        ]);

        $response = $this->getJson("/api/tournaments/{$tournament->slug}");

        $response->assertStatus(200)
            ->assertJsonPath('data.link', 'https://www.facebook.com/ChessPost/555');
    }

    public function test_public_tournament_endpoint_returns_null_link(): void
    {
        $tournament = Tournament::factory()->create([
            'link' => null,
        ]);

        $response = $this->getJson("/api/tournaments/{$tournament->slug}");

        $response->assertStatus(200)
            ->assertJsonPath('data.link', null);
    }

    public function test_my_tournaments_list_includes_link(): void
    {
        Tournament::factory()->create([
            'created_by' => $this->user->id,
            'name' => 'Tournament With Link',
            'link' => 'https://example.com/event',
        ]);

        $response = $this->withHeaders(['Authorization' => "Bearer {$this->token}"])
            ->getJson('/api/my/tournaments');

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('https://example.com/event', $data[0]['link']);
    }

    // ─── ADMIN STORE ───────────────────────────────────────────────

    public function test_admin_can_create_tournament_with_link(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $adminToken = $admin->createToken('admin_token')->plainTextToken;

        $response = $this->withHeaders(['Authorization' => "Bearer {$adminToken}"])
            ->postJson('/api/admin/tournaments', $this->validTournamentData([
                'link' => 'https://facebook.com/admin-event',
            ]));

        $response->assertStatus(201)
            ->assertJsonPath('data.link', 'https://facebook.com/admin-event');
    }

    public function test_admin_can_update_tournament_link(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $adminToken = $admin->createToken('admin_token')->plainTextToken;

        $tournament = Tournament::factory()->create([
            'created_by' => $admin->id,
            'link' => null,
        ]);

        $response = $this->withHeaders(['Authorization' => "Bearer {$adminToken}"])
            ->putJson("/api/admin/tournaments/{$tournament->slug}", [
                'link' => 'https://www.facebook.com/AdminUpdated/111',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.link', 'https://www.facebook.com/AdminUpdated/111');
    }
}
