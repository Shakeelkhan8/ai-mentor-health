<?php

namespace App\Domains\Billing\Services;

use App\Domains\Billing\Enums\SubscriptionStatus;
use App\Domains\Billing\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\UniqueConstraintViolationException;

class SubscriptionSyncService
{
    public function mapStripeStatus(?string $stripeStatus): SubscriptionStatus
    {
        return match ($stripeStatus) {
            'trialing' => SubscriptionStatus::Trialing,
            'active' => SubscriptionStatus::Active,
            'past_due', 'unpaid' => SubscriptionStatus::PastDue,
            'canceled', 'incomplete_expired' => SubscriptionStatus::Canceled,
            default => SubscriptionStatus::Incomplete,
        };
    }

    /**
     * @param  array<string, mixed>  $session
     */
    public function syncFromCheckoutSession(array $session): ?Subscription
    {
        if (data_get($session, 'mode') !== 'subscription') {
            return null;
        }

        $userId = (int) data_get($session, 'metadata.user_id', data_get($session, 'client_reference_id'));
        if ($userId < 1 || ! User::query()->whereKey($userId)->exists()) {
            return null;
        }

        $subscriptionId = data_get($session, 'subscription');
        if (is_array($subscriptionId)) {
            $subscriptionId = $subscriptionId['id'] ?? null;
        }

        $customerId = data_get($session, 'customer');
        if (is_array($customerId)) {
            $customerId = $customerId['id'] ?? null;
        }

        $priceId = data_get($session, 'metadata.price_id')
            ?: config('services.stripe.price_id');

        if (blank($subscriptionId)) {
            return null;
        }

        $paymentStatus = data_get($session, 'payment_status');
        $status = in_array($paymentStatus, ['paid', 'no_payment_required'], true)
            ? SubscriptionStatus::Active
            : SubscriptionStatus::Incomplete;

        return $this->upsertSubscription([
            'user_id' => $userId,
            'stripe_subscription_id' => (string) $subscriptionId,
            'stripe_customer_id' => $customerId ? (string) $customerId : null,
            'stripe_price_id' => $priceId ? (string) $priceId : null,
            'status' => $status,
            'meta' => [
                'checkout_session_id' => data_get($session, 'id'),
                'mode' => data_get($session, 'mode'),
                'payment_status' => $paymentStatus,
            ],
        ]);
    }

    /**
     * @param  array<string, mixed>  $stripeSubscription
     */
    public function syncFromStripeSubscription(array $stripeSubscription): ?Subscription
    {
        $subscriptionId = data_get($stripeSubscription, 'id');
        if (blank($subscriptionId)) {
            return null;
        }

        $userId = (int) data_get($stripeSubscription, 'metadata.user_id');
        $existing = Subscription::query()
            ->where('stripe_subscription_id', $subscriptionId)
            ->first();

        if ($userId < 1) {
            $userId = (int) ($existing?->user_id ?? 0);
        }

        if ($userId < 1) {
            return null;
        }

        $priceId = data_get($stripeSubscription, 'items.data.0.price.id')
            ?: $existing?->stripe_price_id
            ?: config('services.stripe.price_id');

        $customerId = data_get($stripeSubscription, 'customer');
        if (is_array($customerId)) {
            $customerId = $customerId['id'] ?? null;
        }

        $status = $this->mapStripeStatus(data_get($stripeSubscription, 'status'));

        $periodStart = data_get($stripeSubscription, 'current_period_start');
        $periodEnd = data_get($stripeSubscription, 'current_period_end');
        $canceledAt = data_get($stripeSubscription, 'canceled_at');
        $trialEnd = data_get($stripeSubscription, 'trial_end');

        return $this->upsertSubscription([
            'user_id' => $userId,
            'stripe_subscription_id' => (string) $subscriptionId,
            'stripe_customer_id' => $customerId ? (string) $customerId : $existing?->stripe_customer_id,
            'stripe_price_id' => $priceId ? (string) $priceId : null,
            'status' => $status,
            'current_period_starts_at' => $periodStart ? Carbon::createFromTimestamp((int) $periodStart) : null,
            'current_period_ends_at' => $periodEnd ? Carbon::createFromTimestamp((int) $periodEnd) : null,
            'canceled_at' => $canceledAt ? Carbon::createFromTimestamp((int) $canceledAt) : null,
            'trial_ends_at' => $trialEnd ? Carbon::createFromTimestamp((int) $trialEnd) : null,
            'meta' => array_filter([
                'stripe_status' => data_get($stripeSubscription, 'status'),
                'cancel_at_period_end' => data_get($stripeSubscription, 'cancel_at_period_end'),
            ], fn ($v) => $v !== null),
        ]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function upsertSubscription(array $attributes): Subscription
    {
        $subscription = Subscription::query()
            ->where('stripe_subscription_id', $attributes['stripe_subscription_id'])
            ->first();

        if ($subscription) {
            $subscription->fill($attributes)->save();

            return $subscription->fresh();
        }

        try {
            return Subscription::query()->create($attributes);
        } catch (UniqueConstraintViolationException) {
            $subscription = Subscription::query()
                ->where('stripe_subscription_id', $attributes['stripe_subscription_id'])
                ->firstOrFail();

            $subscription->fill($attributes)->save();

            return $subscription->fresh();
        }
    }
}
