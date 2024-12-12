<?php

namespace App\Http\Controllers\API;

use App\Repository\ConversationRepository;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Conversation;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use App\Http\Requests\ConversationRequest;
use App\Http\Resources\ConversationResource;
use App\Service\ConversationService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ConversationsController extends Controller
{
    public function __construct(
        private ConversationRepository $conversationRepository,
        private ConversationService $conversationService
    ) { }

    /**
     * List all conversations
     */
    public function index(): AnonymousResourceCollection
    {
        $user = Auth::guard('sanctum')->user();
        $conversations =  $user->conversations()->wherePivot('user_id', $user->id)->get();
        return ConversationResource::collection($conversations);
    }

    /**
     * Show a specific resource name
     * @return void
     */
    public function show(string $name): Collection
    {
        return Conversation::where('name', 'like', '%' . $name . '%')->get();
    }

    public function store(ConversationRequest $request)
    {
        try {
            $user = $request['auth_user'];
            $participantId = User::where('uuid', $request->participant_id)->firstOrFail();
    
            $conversation = $this->conversationService->createOrUpdateConversation($request, $user, $participantId);
    
            return response()->json([
                'message' => $conversation->wasRecentlyCreated ? 'Conversa criada com sucesso!' : 'Conversa editada com sucesso!',
                'conversation' => $conversation,
            ], $conversation->wasRecentlyCreated ? 201 : 200);

        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Usuário não encontrado.');
            
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Erro inesperado ao iniciar a conversa.',
                'details' => $th->getMessage(),
            ], 500);
        }
    }


}
