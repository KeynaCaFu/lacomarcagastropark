<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use App\Helpers\AuthHelper;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Usar estilos de Bootstrap 5 para la paginación
        Paginator::useBootstrapFive();

        // Compartir el AuthHelper con todas las vistas
        View::share('authHelper', AuthHelper::class);
    }
}
