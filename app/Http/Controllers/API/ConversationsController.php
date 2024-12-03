<?php

namespace App\Http\Controllers\API;

use App\Repository\ConversationRepository;
use Auth;
use App\Models\User;
use App\Models\Conversation;
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

            $user = Auth::guard('sanctum')->user();
            $participantId = User::where('uuid', $request->participant_id)->firstOrFail();
            $existingConversation = $this->conversationRepository->findPrivateConversation($user, $participantId);

            if ($existingConversation === null) {
                $conversation = $this->conversationRepository->create($request, $user, $participantId);
            } else {

                $this->conversationRepository->updateConversation(
                    $existingConversation,
                    $user,
                    $request->name
                );

                return response()->json([
                    'message' => 'Conversa editada com sucesso!',
                    'conversation' => $existingConversation,
                ], 200);
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
            throw new ModelNotFoundException('Usuário não encontrado.');

        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Erro inesperado ao iniciar a conversa.',
                'details' => $th->getMessage(),
            ], 500);
        }
    }


}
