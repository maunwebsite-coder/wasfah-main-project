<?php

namespace App\Providers;

use App\Helpers\Breadcrumbs;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        View::composer('layouts.app', function ($view) {
            $view->with('breadcrumbs', Breadcrumbs::generate());
        });

        View::share('globalContentTranslations', config('content-translations.locales', []));
    }
}
