<?php

namespace App\Domains\Billing\Contracts;

/**
 * Port for Stripe (or other) billing providers.
 */
interface BillingGateway
{
    /**
     * @param  array{
     *   user_id: int,
     *   email: string,
     *   name?: string|null,
     *   price_id: string,
     *   success_url: string,
     *   cancel_url: string,
     *   customer_id?: string|null
     * }  $payload
     * @return array{id: string, url: string}
     */
    public function createSubscriptionCheckout(array $payload): array;

    /**
     * Verify signature (when configured) and return event payload as array.
     *
     * @return array{id: string, type: string, data: array}
     */
    public function parseWebhook(string $payload, ?string $signatureHeader): array;
}
