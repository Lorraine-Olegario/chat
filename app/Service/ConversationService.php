<?php

namespace App\Service;

use App\Http\Requests\ConversationRequest;
use App\Models\User;
use App\Repository\ConversationRepository;

class ConversationService
{
    public function __construct(
        private ConversationRepository $conversationRepository,
        private User $user
    ) { }

    public function createOrUpdateConversation(ConversationRequest $request, User $user, User $participant)
    {
        $existingConversation = $this->conversationRepository->findPrivateConversation($user, $participant);

        if ($existingConversation) {
            $this->conversationRepository->updateConversation(
                $existingConversation,
                $user,
                $request->name
            );
            return $existingConversation;
        }

        return $this->conversationRepository->create($request, $user, $participant);
    }
}