<?php

namespace App\Providers;

use App\Http\Middleware\EnsureTokenIsValid;
use Illuminate\Support\ServiceProvider;

class MiddlewareServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $router = $this->app['router'];
        $router->aliasMiddleware('token.valid', EnsureTokenIsValid::class);
    }
}
