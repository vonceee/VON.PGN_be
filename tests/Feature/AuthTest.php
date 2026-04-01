<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    // ─── REGISTRATION ───────────────────────────────────────────────

    public function test_user_can_register_with_valid_data(): void
    {
        $response = $this->postJson('/api/register', [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'access_token',
                'token_type',
                'user' => ['uid', 'email', 'username'],
            ])
            ->assertJson([
                'message' => 'User successfully registered',
                'token_type' => 'Bearer',
                'user' => [
                    'email' => 'test@example.com',
                    'username' => 'testuser',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'name' => 'testuser',
            'email' => 'test@example.com',
        ]);
    }

    public function test_registration_creates_user_preferences_and_progress(): void
    {
        $this->postJson('/api/register', [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user->preferences);
        $this->assertNotNull($user->progress);
    }

    public function test_registration_returns_valid_sanctum_token(): void
    {
        $response = $this->postJson('/api/register', [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $token = $response->json('access_token');
        $this->assertNotEmpty($token);

        // Token should allow accessing protected routes
        $this->withHeaders(['Authorization' => "Bearer $token"])
            ->getJson('/api/profile')
            ->assertStatus(200);
    }

    public function test_registration_requires_username(): void
    {
        $this->postJson('/api/register', [
            'email' => 'test@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['username']);
    }

    public function test_registration_requires_valid_email(): void
    {
        $this->postJson('/api/register', [
            'username' => 'testuser',
            'email' => 'not-an-email',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_registration_requires_password_confirmation(): void
    {
        $this->postJson('/api/register', [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'WrongPassword',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_registration_requires_minimum_password_length(): void
    {
        $this->postJson('/api/register', [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'Short1',
            'password_confirmation' => 'Short1',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_registration_requires_mixed_case_and_numbers(): void
    {
        // all lowercase
        $this->postJson('/api/register', [
            'username' => 'user1',
            'email' => 'user1@example.com',
            'password' => 'lowercase1',
            'password_confirmation' => 'lowercase1',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['password']);

        // no numbers
        $this->postJson('/api/register', [
            'username' => 'user2',
            'email' => 'user2@example.com',
            'password' => 'OnlyLetters',
            'password_confirmation' => 'OnlyLetters',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_registration_rejects_duplicate_email(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $this->postJson('/api/register', [
            'username' => 'newuser',
            'email' => 'taken@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_registration_rejects_duplicate_username(): void
    {
        User::factory()->create(['name' => 'taken']);

        $this->postJson('/api/register', [
            'username' => 'taken',
            'email' => 'new@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['username']);
    }

    public function test_registration_rejects_invalid_username_characters(): void
    {
        $this->postJson('/api/register', [
            'username' => 'bad user!',
            'email' => 'test@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['username']);
    }

    public function test_registration_accepts_valid_username_formats(): void
    {
        $validUsernames = ['user_name', 'user-name', 'User123'];

        foreach ($validUsernames as $i => $username) {
            $this->postJson('/api/register', [
                'username' => $username,
                'email' => "user{$i}@example.com",
                'password' => 'Password123',
                'password_confirmation' => 'Password123',
            ])->assertStatus(201);
        }
    }

    public function test_registered_user_has_unverified_email(): void
    {
        $response = $this->postJson('/api/register', [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $this->assertNull($response->json('user.email_verified_at'));
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'email_verified_at' => null,
        ]);
    }

    // ─── LOGIN ──────────────────────────────────────────────────────

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('Password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'Password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'access_token',
                'token_type',
                'user' => ['uid', 'email', 'username'],
            ])
            ->assertJson([
                'message' => 'Login successful',
                'token_type' => 'Bearer',
                'user' => [
                    'email' => 'test@example.com',
                    'username' => $user->name,
                ],
            ]);
    }

    public function test_login_returns_valid_sanctum_token(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('Password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'Password123',
        ]);

        $token = $response->json('access_token');

        $this->withHeaders(['Authorization' => "Bearer $token"])
            ->getJson('/api/profile')
            ->assertStatus(200);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('Password123'),
        ]);

        $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'WrongPassword',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_login_fails_with_nonexistent_email(): void
    {
        $this->postJson('/api/login', [
            'email' => 'nobody@example.com',
            'password' => 'Password123',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_login_requires_email(): void
    {
        $this->postJson('/api/login', [
            'password' => 'Password123',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_login_requires_password(): void
    {
        $this->postJson('/api/login', [
            'email' => 'test@example.com',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    // ─── LOGOUT ─────────────────────────────────────────────────────

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson('/api/logout')
            ->assertStatus(200)
            ->assertJson(['message' => 'Successfully logged out']);
    }

    public function test_logout_invalidates_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        // Logout
        $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson('/api/logout')
            ->assertStatus(200);

        // Token should no longer work
        $this->withHeaders(['Authorization' => "Bearer $token"])
            ->getJson('/api/profile')
            ->assertStatus(401);
    }

    // ─── FULL REGISTRATION + LOGIN FLOW ─────────────────────────────

    public function test_register_then_login_flow(): void
    {
        // 1. Register
        $registerResponse = $this->postJson('/api/register', [
            'username' => 'flowuser',
            'email' => 'flow@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $registerResponse->assertStatus(201);
        $registerToken = $registerResponse->json('access_token');

        // 2. Use registration token to access profile
        $this->withHeaders(['Authorization' => "Bearer $registerToken"])
            ->getJson('/api/profile')
            ->assertStatus(200)
            ->assertJsonPath('data.username', 'flowuser');

        // 3. Logout
        $this->withHeaders(['Authorization' => "Bearer $registerToken"])
            ->postJson('/api/logout')
            ->assertStatus(200);

        // 4. Login with same credentials
        $loginResponse = $this->postJson('/api/login', [
            'email' => 'flow@example.com',
            'password' => 'Password123',
        ]);

        $loginResponse->assertStatus(200);
        $loginToken = $loginResponse->json('access_token');

        // 5. Use login token to access profile
        $this->withHeaders(['Authorization' => "Bearer $loginToken"])
            ->getJson('/api/profile')
            ->assertStatus(200)
            ->assertJsonPath('data.username', 'flowuser');
    }
}
