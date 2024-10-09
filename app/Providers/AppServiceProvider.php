<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Spatie\Browsershot\Browsershot;
use Spatie\Crawler\Crawler;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(Browsershot::class, function () {
            $browsershot = new Browsershot;
            $browsershot->noSandbox();

            return $browsershot;
        });

        $this->app->bind(Crawler::class, function ($app) {
            return Crawler::create()
                ->executeJavaScript()
                ->setBrowsershot($app->make(Browsershot::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
