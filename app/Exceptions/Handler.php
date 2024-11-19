<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * Lista de exceções que não devem ser reportadas.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * Lista de entradas que nunca devem ser exibidas.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Reporta ou registra uma exceção.
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Renderiza uma exceção para a resposta HTTP.
     */
    public function render($request, Throwable $exception)
    {
        return parent::render($request, $exception);
    }

    /**
     * Resgistra manipuladores personalizados para exceções.
     *
     * @return void
     */
    public function register(): void
    {
        $this->renderable(function (\Illuminate\Database\QueryException $queryException, $request) {
            return response()->json([
                'error' => 'Erro, problema no banco de dados.',
                'details' => $queryException->getMessage(),
            ], 400);
        });

        $this->renderable(function (\Illuminate\Database\Eloquent\ModelNotFoundException $modelNotFoundException, $request){
            return response()->json([
                'error' => 'Não encontrado.',
                'details' => $modelNotFoundException->getMessage()
            ], 404);
        });
    }
}
