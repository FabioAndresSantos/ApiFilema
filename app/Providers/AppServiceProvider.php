<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
//configuramos el jwt
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //configuramos el largo predeterminado de las cadenas de caracteres en la base de datos.
        Schema::defaultStringLength(191);
    }
}
