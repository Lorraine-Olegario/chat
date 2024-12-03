<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConversationFactory extends Factory
{
    protected $model = Conversation::class;

    public function definition(): array
    {
        return [
            // 'name' => $this->faker->name,
            'type' => $this->faker->randomElement(['private', 'group']),
            // 'user_id' => User::factory(), // Cria um usuário relacionado
            // 'owner_id' => User::factory(), // Define o proprietário
        ];
    }
}
