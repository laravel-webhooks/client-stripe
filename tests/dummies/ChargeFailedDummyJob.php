<?php

declare(strict_types=1);

namespace LaravelWebhooks\Client\Stripe\Tests\Dummies;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ChargeFailedDummyJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle()
    {
    }
}
