<?php

declare(strict_types=1);

namespace LaravelWebhooks\Client\Stripe\Tests;

class FunctionalTestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');

        $app['config']->set('laravel-webhooks.stripe.config', [
            'signing_secrets' => [
                'default' => '123456789',
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getPackageProviders($app)
    {
        return [
            'LaravelWebhooks\Client\ServiceProvider',
            'LaravelWebhooks\Client\Stripe\ServiceProvider',
        ];
    }

    /**
     * Generate an HMAC Signature.
     *
     * @param array $payload
     *
     * @return string
     */
    protected function generateSignature(array $payload)
    {
        $secret = config('laravel-webhooks.stripe.config.signing_secrets.default');

        $timestamp = time();

        $signedPayload = $timestamp.'.'.json_encode($payload);

        $signature = hash_hmac('sha256', $signedPayload, $secret);

        return "t={$timestamp},v1={$signature}";
    }
}
