<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // <--- AGREGA ESTO IMPORTANTE
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\CasillaElectronica;
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
        if($this->app->environment('production')) {
            URL::forceScheme('https');
        }
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $count = CasillaElectronica::where('user_id', Auth::id())
                    ->where('estado', 'no leido')
                    ->count();
                $view->with('notificaciones_sin_leer', $count);
            }
        });
    }
}
