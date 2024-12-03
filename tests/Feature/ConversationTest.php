<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Conversation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Faker\Factory;

class ConversationTest extends TestCase
{
    use RefreshDatabase;

    private string $token;
    private $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->factory = Factory::create();
    }

    public function test_litar_conversar_usuario_logado()
    {
        $user = User::factory()->create();

        $conversation1 = Conversation::factory()->create();
        $conversation2 = Conversation::factory()->create();

        $conversation1->users()->attach($user, ['alias' => 'Migs Ana']);
        $conversation2->users()->attach($user, ['alias' => 'João Silva']);

        //simula que o usuário criado está autenticado.
        $this->actingAs($user);
        $response = $this->getJson('/api/conversations');
        $response->assertStatus(200);

        $response->assertJsonFragment([
            'name' => 'Migs Ana',
            'type' => $conversation1->type,
        ]);

        $response->assertJsonFragment([
            'name' => 'João Silva',
            'type' => $conversation2->type,
        ]);
    }

    public function test_nao_listar_conversas_se_nao_estiver_logado()
    {
        $response = $this->getJson('/api/conversations');
        $response->assertStatus(401);
    }

    public function test_adicionar_uma_conversa()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $user_teste = User::factory()->create();

        $response = $this->postJson('/api/conversations', [
            'name' => $this->factory->name,
            'type' => 'private',
            'participant_id' => $user_teste->uuid
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(structure: [
                "message",
                "conversation" => [
                    "type",
                ]
            ])
            ->assertJsonFragment([
                'message' => 'Conversa criada com sucesso!',
                'type' => 'private',
            ]);

    }

    public function test_adicionar_nome_conversa_ja_criada()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $user_teste = User::factory()->create();

        $response = $this->postJson('/api/conversations', [
            'name' => $this->factory->name,
            'type' => 'private',
            'participant_id' => $user_teste->uuid
        ]);

        $response->assertStatus(201);

        $this->actingAs($user_teste);
        $response = $this->postJson('/api/conversations', [
            'name' => $this->factory->name,
            'type' => 'private',
            'participant_id' => $user->uuid
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(structure: [
                "message",
                "conversation" => [
                    "type",
                ]
            ])
            ->assertJsonFragment([
                'message' => 'Conversa editada com sucesso!',
                'type' => 'private',
            ]);
    }

    public function test_add_or_editar_conversa_com_identificador_errado()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/conversations', [
            'name' => $this->factory->name,
            'type' => 'private',
            'participant_id' => 'fc9e6209-5669-49e1-b9ac-1f38586547baa'
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment([
                'participant_id' => ['O identificador do Participante é inválido.'],
            ]);

    }
}
