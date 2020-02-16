<?php

declare(strict_types=1);

namespace LaravelWebhooks\Client\Stripe;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use LaravelWebhooks\Client\Stripe\Http\Controllers\StripeController;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPublishing();

        Route::macro('stripeWebhooks', function (string $url) {
            return Route::post($url, StripeController::class);
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            realpath(__DIR__.'/../config/config.php'), 'laravel-webhooks.stripe.config'
        );
    }

    /**
     * Register the package publishable resources.
     *
     * @return void
     */
    protected function registerPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                realpath(__DIR__.'/../config/config.php') => config_path('laravel-webhooks/stripe/config.php'),
            ], 'laravel-webhooks:stripe.config');
        }
    }
}
