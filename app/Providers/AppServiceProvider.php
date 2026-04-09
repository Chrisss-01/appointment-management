<?php

namespace App\Providers;

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
        // Railway/PaaS terminates SSL at the proxy — force HTTPS for all generated URLs
        if (config('app.env') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        \Illuminate\Support\Facades\Gate::define('manage-availability', function (\App\Models\User $user) {
            return $user->isStaff() || $user->isAdmin();
        });

        // Register Brevo (Sendinblue) mail transport — uses HTTP API, not SMTP
        \Illuminate\Support\Facades\Mail::extend('brevo', function (array $config) {
            $factory = new \Symfony\Component\Mailer\Bridge\Brevo\Transport\BrevoTransportFactory();

            return $factory->create(
                new \Symfony\Component\Mailer\Transport\Dsn(
                    'brevo+api',
                    'default',
                    config('services.brevo.key')
                )
            );
        });
    }
}
