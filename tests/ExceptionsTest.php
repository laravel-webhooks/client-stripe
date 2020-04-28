<?php

declare(strict_types=1);

namespace LaravelWebhooks\Client\Stripe\Tests;

use Illuminate\Support\Facades\Route;
use LaravelWebhooks\Client\Exceptions\InvalidRequest;
use LaravelWebhooks\Client\Exceptions\InvalidSignature;
use LaravelWebhooks\Client\Stripe\Exceptions\MissingSignature;
use LaravelWebhooks\Client\Stripe\Exceptions\SigningKeyConfigNotFound;

class ExceptionsTest extends FunctionalTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutExceptionHandling();

        Route::stripeWebhook('/webhooks/stripe/{signingKey?}')->name('webhooks.stripe.handle');
    }

    /** @test */
    public function an_exception_will_be_thrown_if_the_signature_is_missing()
    {
        // Arrange
        $this->expectException(MissingSignature::class);
        $this->expectExceptionMessage('The `Stripe-Signature` header is missing.');

        $headers = [];

        // Act
        $this->postJson(route('webhooks.stripe.handle'), [], $headers);
    }

    /** @test */
    public function an_exception_will_be_thrown_if_the_request_is_not_genuine()
    {
        // Arrange
        $this->expectException(InvalidSignature::class);
        $this->expectExceptionMessage('The signature `123456789` found in the `Stripe-Signature` header is invalid.');

        $headers = ['Stripe-Signature' => '123456789'];

        // Act
        $this->postJson(route('webhooks.stripe.handle'), [], $headers);
    }

    /** @test */
    public function an_exception_will_be_thrown_when_the_payload_is_invalid()
    {
        // Arrange
        $this->expectException(InvalidRequest::class);
        $this->expectExceptionMessage('The request is invalid or the payload that was sent is not in a valid format.');

        $payload = [];

        $headers = ['Stripe-Signature' => $this->generateSignature($payload)];

        // Act
        $this->postJson(route('webhooks.stripe.handle'), [], $headers);
    }

    /** @test */
    public function an_exception_will_be_thrown_when_the_signing_key_configuration_is_not_found()
    {
        // Arrange
        $this->expectException(SigningKeyConfigNotFound::class);
        $this->expectExceptionMessage('The configuration for the `other-signing-key` signing key was not found.');

        $payload = [];

        $headers = ['Stripe-Signature' => $this->generateSignature($payload)];

        // Act
        $this->postJson(url('/webhooks/stripe/other-signing-key'), [], $headers);
    }
}
