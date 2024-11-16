<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {

            $user = Auth::user();

            $user->tokens()->delete();
            $token = $user->createToken('API_TOKEN');

            return response()->json([
                'data' => new UserResource($user),
                'token' => $token->plainTextToken
            ], 200);
        }

        return response()->json('Usuário e/ou Senha inváldos', 401);
    }

    public function logout(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        $user->tokens()->delete();

        return response()->json(204);
    }
}
