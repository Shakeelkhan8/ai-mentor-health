<?php

namespace App\Infrastructure\Billing;

use App\Domains\Billing\Contracts\BillingGateway;
use App\Domains\Shared\Exceptions\DomainException;
use Stripe\Checkout\Session;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\Webhook;
use Throwable;

class StripeBillingGateway implements BillingGateway
{
    public function createSubscriptionCheckout(array $payload): array
    {
        $secret = config('services.stripe.secret');
        if (blank($secret)) {
            throw new DomainException(
                'Stripe is not configured. Set STRIPE_SECRET in your environment.',
                'stripe_not_configured',
            );
        }

        $priceId = $payload['price_id'] ?? config('services.stripe.price_id');
        if (blank($priceId)) {
            throw new DomainException(
                'Stripe price is not configured. Set STRIPE_PRICE_ID in your environment.',
                'stripe_price_missing',
            );
        }

        Stripe::setApiKey($secret);

        try {
            $params = [
                'mode' => 'subscription',
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price' => $priceId,
                    'quantity' => 1,
                ]],
                'success_url' => $payload['success_url'],
                'cancel_url' => $payload['cancel_url'],
                'client_reference_id' => (string) $payload['user_id'],
                'metadata' => [
                    'user_id' => (string) $payload['user_id'],
                    'price_id' => (string) $priceId,
                ],
                'subscription_data' => [
                    'metadata' => [
                        'user_id' => (string) $payload['user_id'],
                    ],
                ],
            ];

            if (! empty($payload['customer_id'])) {
                $params['customer'] = $payload['customer_id'];
            } else {
                $params['customer_email'] = $payload['email'];
            }

            $session = Session::create($params);
        } catch (Throwable $e) {
            throw new DomainException(
                'Unable to start Stripe Checkout.',
                'stripe_checkout_failed',
                previous: $e,
            );
        }

        if (blank($session->url)) {
            throw new DomainException('Stripe Checkout session has no URL.', 'stripe_checkout_url_missing');
        }

        return [
            'id' => (string) $session->id,
            'url' => (string) $session->url,
        ];
    }

    public function parseWebhook(string $payload, ?string $signatureHeader): array
    {
        $secret = config('services.stripe.webhook_secret');

        try {
            if (filled($secret)) {
                if (blank($signatureHeader)) {
                    throw new DomainException('Missing Stripe signature.', 'stripe_signature_missing');
                }

                $event = Webhook::constructEvent($payload, $signatureHeader, $secret);
            } else {
                // Local/dev without webhook secret — parse only (never use in production).
                if (! app()->environment(['local', 'testing'])) {
                    throw new DomainException(
                        'STRIPE_WEBHOOK_SECRET is required outside local/testing.',
                        'stripe_webhook_secret_missing',
                    );
                }

                $decoded = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
                if (! is_array($decoded)) {
                    throw new DomainException('Invalid webhook payload.', 'stripe_webhook_invalid');
                }

                return [
                    'id' => (string) ($decoded['id'] ?? ''),
                    'type' => (string) ($decoded['type'] ?? ''),
                    'data' => (array) ($decoded['data'] ?? []),
                ];
            }
        } catch (SignatureVerificationException $e) {
            throw new DomainException('Invalid Stripe webhook signature.', 'stripe_signature_invalid', previous: $e);
        } catch (DomainException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new DomainException('Unable to parse Stripe webhook.', 'stripe_webhook_parse_failed', previous: $e);
        }

        $array = $event->toArray();

        return [
            'id' => (string) ($array['id'] ?? ''),
            'type' => (string) ($array['type'] ?? ''),
            'data' => (array) ($array['data'] ?? []),
        ];
    }
}
