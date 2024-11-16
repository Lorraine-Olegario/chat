<?php

namespace App\Http\Controllers;

use App\Http\Requests\MessagesRequest;
use App\Http\Resources\MessageResource;
use App\Models\Conversation;
use App\Models\ConversationUser;
use App\Models\Message;
use App\Models\User;
use Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MessagesController extends Controller
{
    public function exibirTodasMensagensConversa(int $idConversation): AnonymousResourceCollection
    {
        $messages = Message::where('conversation_id', $idConversation)->get();
        return MessageResource::collection($messages);
    }

    #envia uma mensagem
    public function store(MessagesRequest $request)
    {
        try {

            $user = Auth::guard('sanctum')->user();
            User::where('id', $user->id)->firstOrFail();

            $teste = ConversationUser::where('user_id', $request->mensageiro)
                ->where('conversation_id', $request->conversation_id)
                ->first();

            if (empty($teste)) {
                $conversation = Conversation::where('id', $request->conversation_id)->get();
                $conversation->users()->syncWithoutDetaching([$request->mensageiro => ['joined_at' => now()]]);
            }


            $teste2 = ConversationUser::where('user_id', $user->id)
                ->where('conversation_id', $request->conversation_id)
                ->first();

            if (empty($teste2)) {
                $conversation2 = Conversation::where('id', $request->conversation_id)->get();
                $conversation2->users()->syncWithoutDetaching([$user->id => ['joined_at' => now()]]);
            }


            //ADD CONVERSA
            $message = new Message();
            $message->fill($request->all());
            $message->user_id = $user->id;
            $message->save();

            return response()->json([
                'message' => 'Mensagem enviada com sucesso!',
                'conversation' => $message,
            ], 201);

        } catch (\Throwable $th) {

            return response()->json([
                'error' => 'Erro ao enviar mensagem: ' . $th->getMessage()
            ], 500);
        }
    }
}
