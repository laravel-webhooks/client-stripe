<?php

declare(strict_types=1);

namespace LaravelWebhooks\Client\Stripe\Http\Controllers;

use LaravelWebhooks\Client\Exceptions\InvalidRequest;
use LaravelWebhooks\Client\Http\Controllers\WebhookClientController;
use LaravelWebhooks\Client\Stripe\Http\Middleware\StripeVerifySignature;

class StripeController extends WebhookClientController
{
    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(StripeVerifySignature::class);
    }

    /**
     * {@inheritdoc}
     */
    public function validateRequest(): void
    {
        if (empty($this->request->input()) || ! $this->request->input('type')) {
            throw new InvalidRequest();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getWebhookType(): string
    {
        return $this->request->input('type');
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinedJobs(): array
    {
        return config('laravel-webhooks.stripe.config.jobs', []);
    }
}
