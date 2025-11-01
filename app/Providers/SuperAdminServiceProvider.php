<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class SuperAdminServiceProvider extends ServiceProvider
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
        // Interceptar todas las verificaciones de permisos para superadministradores
        Gate::before(function ($user, $ability) {
            // Si el usuario es superadministrador, permitir todo
            if ($user && $user->isSuperAdmin()) {
                return true;
            }
        });
    }
}
