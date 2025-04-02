<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Middleware\CheckRole;
use Illuminate\Http\Request;
use App\Http\Middleware\ForceJsonResponse;
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'jwt.auth' => JwtMiddleware::class,  // Đăng ký middleware với alias 'jwt.auth'
            'force.json' => ForceJsonResponse::class, // Đăng ký middleware với alias 'force.json'
            'role' => CheckRole::class, // Đăng ký middleware với alias 'role'
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
    })
    ->withProviders([
        \App\Providers\RouteServiceProvider::class,
    ])
    ->create();
