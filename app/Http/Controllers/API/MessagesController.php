<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\MessagesRequest;
use App\Http\Resources\MessageResource;
use App\Models\ConversationUser;
use App\Models\Message;
use Auth;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Controllers\Controller;

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

            ConversationUser::where('user_id', $user->id)
                ->where('conversation_id', $request->conversation_id)
                ->firstOrFail();

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

    public function edit(int $idMessage, MessagesRequest $request)
    {
        try {
            $user = Auth::guard('sanctum')->user();

            $message = Message::where('id', $idMessage)
                ->where('user_id', $user->id)
                ->where('conversation_id', $request->conversation_id)
                ->firstOrFail();

            ConversationUser::where('user_id', $user->id)
                    ->where('conversation_id', $request->conversation_id)
                    ->firstOrFail();

            $message->content = $request['content'];
            $message->save();

            return response()->json([
                'message' => 'Mensagem editada com sucesso!',
                'conversation' => $message,
            ], 201);

        } catch (\Throwable $th) {

            return response()->json([
                'error' => 'Erro ao editar mensagem: ' . $th->getMessage()
            ], 500);
        }
    }
}
