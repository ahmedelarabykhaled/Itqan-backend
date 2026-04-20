<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app['router']->prefix('api/v1')->group(function () {

            $this->app['router']->prefix('customers')->group(function () {
                $this->app['router']->prefix('auth')->group(
                    base_path('routes/api/customers/auth.php')
                );
                $this->app['router']->prefix('')->group(
                    base_path('routes/api/customers/customers.php')
                );
                $this->app['router']->prefix('')->group(
                    base_path('routes/api/customers/memorized.php')
                );
            });

            $this->app['router']->prefix('locations')->group(
                base_path('routes/api/locations.php')
            );
        });
    }
}
