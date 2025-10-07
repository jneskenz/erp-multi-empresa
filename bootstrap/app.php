<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'superadmin' => \App\Http\Middleware\SuperAdminMiddleware::class,
            'superusuario' => \App\Http\Middleware\VerificarSuperusuario::class,
            'grupo.access' => \App\Http\Middleware\VerificarAccesoGrupo::class,
            'empresa.access' => \App\Http\Middleware\VerificarAccesoEmpresa::class,
            'rol.administrador' => \App\Http\Middleware\VerificarRolAdministrador::class,
            'contexto.empresa' => \App\Http\Middleware\VerificarContextoEmpresa::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
