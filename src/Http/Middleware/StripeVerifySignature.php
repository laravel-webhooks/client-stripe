<?php

declare(strict_types=1);

namespace LaravelWebhooks\Client\Stripe\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use LaravelWebhooks\Client\Stripe\Exceptions\InvalidSignature;
use LaravelWebhooks\Client\Stripe\Exceptions\MissingSignature;

class StripeVerifySignature
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @throws \LaravelWebhooks\Client\Stripe\Exceptions\MissingSignature
     * @throws \LaravelWebhooks\Client\Stripe\Exceptions\InvalidSignature
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $stripeSignature = $request->header('Stripe-Signature');

        if (! $stripeSignature) {
            throw new MissingSignature();
        }

        $signingSecret = $this->getSigningSecret($request);

        $timestamp = $this->extractTimestamp($stripeSignature);

        $hashMac = hash_hmac('sha256', $timestamp.'.'.$request->getContent(), $signingSecret);

        $foundValidSignature = false;

        foreach ($this->extractSignatures($stripeSignature) as $signature) {
            if (hash_equals($hashMac, $signature)) {
                $foundValidSignature = true;

                break;
            }
        }

        if ($foundValidSignature === false) {
            throw new InvalidSignature($stripeSignature);
        }

        return $next($request);
    }

    /**
     * Returns the proper signing secret.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return string
     */
    protected function getSigningSecret(Request $request): string
    {
        $secrets = config('laravel-webhooks.stripe.config.signing_secrets');

        $signingKey = $request->route('signingKey') ?? 'default';

        return $secrets[$signingKey] ?? $secrets[0];
    }

    /**
     * Extract the timestamp from the Stripe Header.
     *
     * @param string $header
     *
     * @return int
     */
    protected function extractTimestamp(string $header): int
    {
        $items = explode(',', $header);

        foreach ($items as $item) {
            $parts = explode('=', $item, 2);

            if ($parts[0] === 't') {
                if (! is_numeric($parts[1])) {
                    return -1;
                }

                return (int) ($parts[1]);
            }
        }

        return -1;
    }

    /**
     * Extract the signatures from the Stripe Header.
     *
     * @param string $header
     * @param string $scheme
     *
     * @return array
     */
    protected function extractSignatures(string $header, string $scheme = 'v1'): array
    {
        $signatures = [];

        $items = explode(',', $header);

        foreach ($items as $item) {
            $parts = explode('=', $item, 2);

            if ($parts[0] === $scheme) {
                array_push($signatures, $parts[1]);
            }
        }

        return $signatures;
    }
}
