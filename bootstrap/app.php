<?php

use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Route middleware takma adlari
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'setlocale' => SetLocale::class,
        ]);

        // Tum rotalar {locale} on eki altinda oldugu icin "login" ve "dashboard"
        // yonlendirmeleri de dil parametresini tasimak zorundadir.
        $middleware->redirectGuestsTo(
            fn (Request $request) => route('login', ['locale' => $request->route('locale') ?? 'en'])
        );

        $middleware->redirectUsersTo(
            fn (Request $request) => route('dashboard', ['locale' => session('locale', 'en')])
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
