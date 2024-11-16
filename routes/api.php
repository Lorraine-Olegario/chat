<?php

use App\Http\Controllers\ConversationsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/login',[AuthController::class, 'login']);
Route::post('/user',[UserController::class, 'store']);
Route::get('/user',[UserController::class, 'index']);

Route::middleware(['token.valid'])->group( function () {

    Route::post('/logout',[AuthController::class, 'logout']);

    Route::post('/conversations',[ConversationsController::class, 'store']); //cadastra
    Route::get('/conversations',[ConversationsController::class, 'index']);
    Route::get('/conversations/{name}',[ConversationsController::class, 'show']);
    Route::get('/conversations/{id}',[ConversationsController::class, 'listConversation']);

    Route::post('/message',[MessagesController::class, 'store']);
    Route::get('/message/{idConversation}',[MessagesController::class, 'exibirTodasMensagensConversa']);
});