<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();


if(!empty($_SERVER['HTTP_HOST']) && str_contains($_SERVER['HTTP_HOST'], 'localhost') === false &&  str_contains($_SERVER['HTTP_HOST'], '127.0.0.1') === false) {
    $domain = $_SERVER['HTTP_HOST'];
    if (isset($domain))
        $app->loadEnvironmentFrom('.env.'.$domain);

}
return $app;

