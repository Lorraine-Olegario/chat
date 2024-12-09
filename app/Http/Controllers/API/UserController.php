<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * List all
     * @return void
     */
    public function index(): AnonymousResourceCollection
    {
        $users = User::all();
        return UserResource::collection($users);
    }

    /**
     * Show a specific resource
     * @return void
     */
    public function show()
    {

    }

    public function store(UserRequest $request)
    {
        $user =  User::create($request->all());
        $token = $user->createToken('API_TOKEN');

        return response()->json([
            'data' => $user,
            'token' => $token->plainTextToken
        ], 201);
    }
}
