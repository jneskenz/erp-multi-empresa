<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class CustomizationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Cargar helpers de personalización
        require_once app_path('Helpers/CustomizationHelper.php');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
