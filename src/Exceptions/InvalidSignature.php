<?php

declare(strict_types=1);

namespace LaravelWebhooks\Client\Stripe\Exceptions;

use LaravelWebhooks\Client\Exceptions\InvalidSignature as BaseInvalidSignatureException;

class InvalidSignature extends BaseInvalidSignatureException
{
    /**
     * Constructor.
     *
     * @param string $signature
     *
     * @return void
     */
    public function __construct(string $signature)
    {
        parent::__construct('The signature `%s` found in the `Stripe-Signature` header is invalid.', $signature);
    }
}
