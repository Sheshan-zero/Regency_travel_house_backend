<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Auth\Notifications\ResetPassword;

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
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
        ResetPassword::createUrlUsing(function ($user, string $token) {
            return "http://localhost:5173/reset-password?token={$token}&email=" . urlencode($user->email);
        });

        ResetPassword::createUrlUsing(function ($user, string $token) {
            return "http://localhost:5173/reset-password/staff?token={$token}&email=" . urlencode($user->email);
        });
        

    }
}
