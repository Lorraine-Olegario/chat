<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\MessagesRequest;
use App\Http\Resources\MessageResource;
use App\Models\ConversationUser;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Controllers\Controller;
use App\Service\MessageService;

class MessagesController extends Controller
{
    public function __construct(
        private MessageService $messageService
    ) { }

    /**
     * List all conversations
     */
    public function index(int $idConversation): AnonymousResourceCollection
    {
        $messages = Message::where('conversation_id', $idConversation)->get();
        return MessageResource::collection($messages);
    }

    public function store(MessagesRequest $request)
    {
        try {
            $message = $this->messageService->send($request->all());
            
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
            $message = $this->messageService->update($idMessage, $request->all());

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
