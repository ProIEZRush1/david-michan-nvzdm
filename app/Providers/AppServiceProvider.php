<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
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
        Vite::prefetch(concurrency: 3);

        // Force https URL generation only when APP_URL is actually https — not whenever
        // APP_ENV=production. Coolify's proxy terminates TLS externally while this container
        // always serves plain HTTP internally (php artisan serve on :8080), so keying off the
        // environment name alone forced https:// on every asset/route URL even when nothing is
        // listening on https — including in local/CI smoke tests that also run with
        // APP_ENV=production but serve over http://127.0.0.1, breaking every asset load.
        if (str_starts_with(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }
    }
}
