<?php

declare(strict_types=1);

namespace LaravelWebhooks\Client\Stripe\Exceptions;

use LaravelWebhooks\Client\Exceptions\MissingSignature as BaseMissingSignature;

class MissingSignature extends BaseMissingSignature
{
    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct('The `Stripe-Signature` header is missing.');
    }
}
