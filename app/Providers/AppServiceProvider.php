<?php

namespace App\Providers;

use App\Models\SiteSetting;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
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
        $locale = config('app.locale', 'tr');
        Carbon::setLocale($locale);
        CarbonImmutable::setLocale($locale);

        View::composer(['auth.*', 'pages.*', 'layouts.app'], function ($view) {
            $view->with('homepage_theme', SiteSetting::get('homepage_theme', 'theme_1'));
        });
    }
}
