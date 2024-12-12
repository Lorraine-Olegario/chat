<?php

namespace App\Service;

use App\Models\ConversationUser;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class MessageService
{
    public function __construct(
        private ConversationUser $conversationUser,
        private Message $message
    ) { }

    public function send(array $data): Message
    {
        $idUser = $data['auth_user']->id;

        $this->conversationUser::where('user_id', $idUser)
            ->where('conversation_id', $data['conversation_id'])
            ->firstOrFail();

        $message = new Message();
        $message->fill($data);
        $message->user_id = $idUser;
        $message->save();

        return $message;
    }

    public function update(int $messageId, array $data): Message
    {
        
        $idUser = $data['auth_user']->id;

        $message = $this->message::where('id', $messageId)
            ->where('user_id', $idUser)
            ->where('conversation_id', $data['conversation_id'])
            ->firstOrFail();

        $message->content = $data['content'];
        $message->save();

        return $message;
    }
}
