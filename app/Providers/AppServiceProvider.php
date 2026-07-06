<?php

namespace App\Providers;

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

        // Deliberately NOT calling URL::forceScheme('https') here: `trustProxies(at: '*')` in
        // bootstrap/app.php already makes the request scheme follow Coolify's X-Forwarded-Proto
        // header, so asset()/route() naturally render https in production without hardcoding it.
        // Forcing it unconditionally (even only when config('app.url') happens to be https, which
        // it legitimately is in production) broke every asset load in local/CI smoke tests, which
        // share that same https APP_URL but are served directly over plain http://127.0.0.1.
    }
}
