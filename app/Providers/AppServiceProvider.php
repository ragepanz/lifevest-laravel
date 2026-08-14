<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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
        // Force HTTPS scheme for asset URLs when accessed via proxy
        // (e.g., ngrok, Cloudflare Tunnel) so the browser doesn't block
        // them due to mixed content warnings on mobile devices.
        if (request()->server('HTTP_X_FORWARDED_PROTO') === 'https' || request()->isSecure()) {
            URL::forceScheme('https');
        }
    }
}
