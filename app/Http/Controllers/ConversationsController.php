<?php

namespace App\Http\Controllers;

use App\Repository\ConversationRepository;
use Auth;
use App\Models\User;
use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Http\Request;
use App\Models\ConversationUser;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use App\Http\Requests\ConversationRequest;
use App\Http\Resources\ConversationResource;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ConversationsController extends Controller
{
    public function __construct(
        private ConversationRepository $conversationRepository
    ) { }


    /**
     * List all conversations
     * @return void
     */
    public function index(): AnonymousResourceCollection
    {
        $user = Auth::guard('sanctum')->user();
        $conversations = Conversation::where('owner_id', $user->id)->get();
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

            $user = Auth::guard('sanctum')->user();
            $participantId = User::where('uuid', $request->participant_id)->firstOrFail();
            $existingConversation = $this->conversationRepository->findPrivateConversation($user, $participantId);

            if ($existingConversation === null) {
                $conversation = $this->conversationRepository->create($request, $user, $participantId);
            } else {
                $existingConversation->users()->syncWithoutDetaching([
                    $user->id => ['alias' => $request->name, 'joined_at' => now()],
                ]);

                $conversation = $existingConversation;
            }

            return response()->json([
                'message' => 'Conversa criada com sucesso!',
                'conversation' => $conversation,
            ], 201);

        } catch (QueryException $e) {
            return response()->json([
                'error' => 'Erro ao iniciar uma conversa: problema no banco de dados.',
                'details' => $e->getMessage(),
            ], 400);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Usuário não encontrado.',
            ], 404);

        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Erro inesperado ao iniciar a conversa.',
                'details' => $th->getMessage(),
            ], 500);
        }
    }


}
