<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use App\Http\Middleware\JwtMiddleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api([
            'jwt.auth' => JwtMiddleware::class,  // Đăng ký middleware với alias 'jwt.auth'
        ]);
        
    })
    ->withExceptions(function (Exceptions $exceptions) {
    })
    ->withProviders([
        \App\Providers\RouteServiceProvider::class,
    ])
    ->create();
