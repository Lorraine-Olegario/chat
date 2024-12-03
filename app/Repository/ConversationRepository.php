<?php

namespace App\Repository;

use App\Models\Conversation;
use App\Models\User;

class ConversationRepository
{
    public function findPrivateConversation($user, $participant): ?Conversation
    {
        return Conversation::where('type', 'private')
            ->whereHas('users', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->whereHas('users', function ($query) use ($participant) {
                $query->where('user_id', $participant->id);
            })
            ->first();
    }

    public function create($request, $user, $participantId): Conversation
    {
        $conversation = new Conversation();
        $conversation->fill($request->except('name'));
        $conversation->save();

        $conversation->users()->syncWithoutDetaching([
            $user->id => ['joined_at' => now(), 'alias' => $request->name],
            $participantId->id => ['joined_at' => now()],
        ]);

        return $conversation;
    }

    public function updateConversation(Conversation $conversation, User $user, string $alias)
    {
        $conversation->users()->syncWithoutDetaching([
            $user->id => ['alias' => $alias, 'joined_at' => now()],
        ]);
    }
}
