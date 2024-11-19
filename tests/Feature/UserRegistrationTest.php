<?php

namespace Tests\Feature;

use Faker\Factory;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserRegistrationTest extends TestCase
{
    use RefreshDatabase;
    private $faker;

    public function setUp(): void
    {
        parent::setUp();
        $this->faker = Factory::create();
    }

    /**
     * A basic feature test example.
     */
    public function test_user_can_register_with_valid_data(): void
    {
        $email = $this->faker->email();
        $response = $this->json('POST', '/api/user',[
            'name' => $this->faker->name(),
            'email' => $email,
            'password' => 'senhaSegura654',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => $email]);
        $response->assertHeader('Content-Type', 'application/json');
    }

    public function test_user_cannot_register_with_duplicate_email()
    {
        $email = $this->faker->unique()->safeEmail();
        $response = $this->json('POST', '/api/user',[
            'name' => $this->faker->name(),
            'email' => $email,
            'password' => 'senhaSegura654',
        ]);

        $response->assertStatus(201);

        $duplicateResponse = $this->json('POST', '/api/user', [
            'name' => $this->faker->name(),
            'email' => $email,
            'password' => 'senhaSegura654',
        ]);

        $duplicateResponse
            ->assertStatus(422)
            ->assertJsonFragment([
                'message' => 'O email já está em uso.',
            ]);

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'email' => $email,
        ]);
    }

    public function test_user_cannot_register_with_invalid_email()
    {
        $response = $this->json('POST', '/api/user', [
            'name' => $this->faker->name(),
            'email' => 'emailErrado@',
            'password' => 'senhaSegura654',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonFragment([
                'message' => 'Por favor, insira um e-mail válido.',
            ]);
    }

    public function test_user_cannot_register_with_weak_password()
    {
        $response = $this->json('POST', '/api/user', [
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'password' => 'senha',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonFragment([
                'message' => 'A senha deve ter pelo menos 6 caracteres.',
            ]);
    }

    public function test_user_cannot_register_with_missing_required_fields()
    {
        $response = $this->json('POST', '/api/user',[
            'email' => $this->faker->unique()->safeEmail(),
            'password' => 'senhaSegura654',
        ]);
        $response->assertStatus(422)
            ->assertJsonFragment([
                'message' => 'O campo nome é obrigatório.',
            ]);

        $response = $this->json('POST', '/api/user',[
            'name' => $this->faker->name(),
            'password' => 'senhaSegura654',
        ]);
        $response->assertStatus(422)
            ->assertJsonFragment([
                'message' => 'O campo e-mail é obrigatório.',
            ]);

        $response = $this->json('POST', '/api/user',[
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
        ]);
        $response->assertStatus(422)
            ->assertJsonFragment([
                'message' => 'O campo senha é obrigatório.',
            ]);
    }
}
