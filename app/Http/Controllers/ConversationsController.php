<?php

namespace App\Http\Controllers;

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

            $conversation = new Conversation();
            $user = Auth::guard('sanctum')->user();

            //IDENTIFICA 2 USUARIO PELO NUMERO
            $identify = $request->participant_id;
            $participantId = User::where('uuid', $identify)->firstOrFail();

            $existingConversation = $this->validaSeJaTemUmaConversa($user, $participantId);
            if ($existingConversation === null) {
                $conversation = $this->cadastraConversa($request, $user, $participantId);
            } else {
                $conversation = $existingConversation->users()->syncWithoutDetaching([
                    $user->id => ['alias' => $request->name, 'joined_at' => now()],
                ]);
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

    public function validaSeJaTemUmaConversa($user, $participant)
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

    public function cadastraConversa($request, $user, $participantId)
    {
        //ADD CONVERSA
        $conversation = new Conversation();
        $conversation->fill($request->except('name'));
        $conversation->save();

        //um forma RUIM de validar se o id do token existe
        User::where('id', $user->id)->firstOrFail();

        $conversation->users()->syncWithoutDetaching([
            $user->id => ['joined_at' => now(), 'alias' => $request->name],
            $participantId->id => ['joined_at' => now()],
        ]);

        return $conversation;
    }
}
