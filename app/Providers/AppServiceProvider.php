<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

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
        RateLimiter::for('login', function (Request $request) {
            $email = Str::lower((string) $request->input('email'));
            $ip = $request->ip;

            return Limit::perMinute(5)->by($email.'|'.$ip)
                ->response(function (Request $request, array $headers) {

                    return response()->json([
                        'status' => 'error',
                        'message' => 'Demasiados intentos de inicio de sesión. Inténtalo nuevamente en un minuto.',
                    ], 429, $headers);
                });
        });
    }
}
