<?php

declare(strict_types=1);

namespace LaravelWebhooks\Client\Stripe\Tests;

use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;
use LaravelWebhooks\Client\Models\WebhookReceived;
use LaravelWebhooks\Client\Stripe\Tests\Dummies\ChargeFailedDummyJob;

class DispatchingJobsTest extends FunctionalTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutExceptionHandling();

        Route::stripeWebhooks('/webhooks/stripe')->name('webhooks.stripe.handle');
    }

    /** @test */
    public function job_will_be_dispatched_if_the_type_is_defined()
    {
        // Arrange
        Bus::fake();

        config(['laravel-webhooks.stripe.config.jobs' => [
            'charge.failed' => ChargeFailedDummyJob::class,
        ]]);

        $payload = $this->loadFixture('charge.failed');

        $headers = ['Stripe-Signature' => $this->generateSignature($payload)];

        // Act
        $response = $this->postJson(route('webhooks.stripe.handle'), $payload, $headers);

        // Assert
        $response->assertStatus(Response::HTTP_OK);

        $this->assertCount(1, WebhookReceived::get());

        Bus::assertDispatched(ChargeFailedDummyJob::class, 1);
    }

    /** @test */
    public function job_will_not_be_dispatched_if_the_type_is_not_defined()
    {
        // Arrange
        Bus::fake();

        $payload = $this->loadFixture('charge.failed');

        $headers = ['Stripe-Signature' => $this->generateSignature($payload)];

        // Act
        $response = $this->postJson(route('webhooks.stripe.handle'), $payload, $headers);

        // Assert
        $response->assertStatus(Response::HTTP_OK);

        $this->assertCount(0, WebhookReceived::get());

        Bus::assertNotDispatched(ChargeFailedDummyJob::class);
    }
}
