<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Services\ServiceBase\GetBaseParamsService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $debugIps = array_filter(
            array_map('trim', explode(',', env('DEBUG_IPS', '')))
        );

        if (in_array(request()->ip(), $debugIps, true)) {
            config(['app.debug' => true]);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        View::composer('*', fn ($view) => $view->with('baseParams', app(GetBaseParamsService::class)->execute()) );
    }
}