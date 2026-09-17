<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_and_receive_token(): void
    {
        $user = User::factory()->create([
            'email' => 'jose@example.com',
            'password' => 'password123',
        ]);

        $payload = [
            'email' => 'jose@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson("/api/v1/login", $payload);

        $response->assertOk();

        $response->assertJsonStructure([
            'user' => [
                'id',
                'name',
                'email',
            ],
            'token',
        ]);

        $response->assertJsonPath('user.id', $user->id);
        $response->assertJsonPath('user.name', $user->name);
        $response->assertJsonPath('user.email', $user->email);
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken('stockcore-api');

        $response = $this->withToken($token->plainTextToken)->postJson('/api/v1/logout');

        $response->assertOk();

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $token->accessToken->id,
        ]);
    }
}
