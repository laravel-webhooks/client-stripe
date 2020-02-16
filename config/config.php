<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Webhook Signing Secrets
    |--------------------------------------------------------------------------
    |
    | Stripe will send all the Webhooks with a signature which will be
    | used to verify it's authenticity against the sent payload.
    |
    | Since Stripe allows an account to have multiple webhooks defined,
    | setting many signing secrets is totally possible here as well.
    |
    | More information here:
    | - https://open-source.werxe.com/laravel-webhooks/clients/stripe/signatures.html
    |
    */

    'signing_secrets' => [
        'default' => env('STRIPE_WEBHOOK_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Jobs
    |--------------------------------------------------------------------------
    |
    | Define here the jobs that should be dispatched for
    | each Stripe event you might want to listen for.
    |
    | The "key" is the Stripe event name, the value is the
    | job you wish to dispatch for that particular event.
    |
    | For a list of valid event names, please visit:
    | - https://stripe.com/docs/api#event_types
    |
    */

    'jobs' => [
        // 'customer.subscription.created' => \App\Jobs\Stripe\SubscriptionCreated::class,
    ],
];
