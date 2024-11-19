<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $loginResponse;
    private $token;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'password' => bcrypt($password = 'i-love-laravel'),
        ]);

        $this->loginResponse = $this->post('/api/login', [
            'email' => $this->user->email,
            'password' => $password,
        ]);

        $this->token = $this->loginResponse->json('token');
    }
    public function test_user_can_logout_with_token()
    {
        $this->assertAuthenticatedAs($this->user);
        $this->loginResponse->assertStatus(200)
            ->assertJsonStructure([
                'token',
            ]);

        $logoutResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->post('/api/logout');

        $logoutResponse->assertStatus(204);
    }

    // public function test_user_cannot_logout_with_invalid_token()
    // {
    //     $logoutResponse = $this->withHeaders([
    //         'Authorization' => 'Bearer ' . 'invali',
    //     ])->post('/api/logout');

    //     $logoutResponse->assertStatus(401)
    //         ->assertJson([
    //             'message' => 'Unauthorized',
    //         ]);
    // }
}
