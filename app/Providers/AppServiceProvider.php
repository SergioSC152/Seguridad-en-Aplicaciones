<?php

namespace App\Providers;

use App\Models\Media;
use App\Models\User;
use App\Services\MailSettingsService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
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
        // Permite que la aplicación arranque antes de ejecutar la migración inicial.
        if (Schema::hasTable('mail_settings')) {
            app(MailSettingsService::class)->apply();
        }

        Gate::define('permission', fn (User $user, string $permission): bool => $user->hasPermissionTo($permission));

        View::composer('partials.cowapp-logo', function (\Illuminate\View\View $view): void {
            $logo = Schema::hasTable('media')
                ? Media::query()->where('name', Media::COWAPP_LOGO_NAME)->where('active', true)->first()
                : null;

            // Una URL relativa conserva el host y puerto de la sesión actual.
            $view->with('cowappLogoUrl', $logo?->url);
        });

        RateLimiter::for('login', function (Request $request) {
            $email = Str::lower((string) $request->input('email'));

            return Limit::perMinute(5)
                ->by($email.'|'.$request->ip());
        });
        RateLimiter::for('login-burst', fn (Request $request) => Limit::perMinute(30)->by($request->ip()));

        RateLimiter::for('otp-send', function (Request $request) {
            $email = Str::lower((string) ($request->input('email') ?? $request->session()->get('password_reset_email')));

            return [
                Limit::perMinutes(15, 3)
                    ->by(hash('sha256', $email.'|'.$request->ip())),
                Limit::perMinutes(15, 10)
                    ->by('otp-send-ip:'.$request->ip()),
            ];
        });

        RateLimiter::for('otp-verify', function (Request $request) {
            $email = Str::lower((string) $request->session()->get('password_reset_email'));

            return [
                Limit::perMinute(5)
                    ->by(hash('sha256', $email.'|'.$request->ip())),
                Limit::perMinute(20)
                    ->by('otp-verify-ip:'.$request->ip()),
            ];
        });

        RateLimiter::for('mail-settings-update', function (Request $request) {
            return Limit::perMinute(5)->by('mail-settings:'.$request->user()?->getAuthIdentifier());
        });
    }
}
