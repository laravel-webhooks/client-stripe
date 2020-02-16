<?php

declare(strict_types=1);

namespace LaravelWebhooks\Client\Stripe\Tests;

class FunctionalTestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate', ['--database' => 'testing'])->run();
    }

    /**
     * {@inheritdoc}
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');

        $app['config']->set('laravel-webhooks.stripe.config.signing_secrets', [
            'default' => '123456789',
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

    /**
     * Reads the contents of the given test fixture.
     *
     * @param string $name
     *
     * @throws \Exception
     *
     * @return array
     */
    protected function loadFixture(string $name): array
    {
        $file = __DIR__."/fixtures/{$name}.json";

        if (file_exists($file)) {
            return json_decode(file_get_contents($file), true);
        }

        throw new \Exception('Fixture was not found!');
    }
}
