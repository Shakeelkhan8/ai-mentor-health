<?php

namespace App\Domains\Billing\Actions;

use App\Domains\Billing\Contracts\BillingGateway;
use App\Domains\Shared\Actions\Action;
use App\Domains\Shared\Exceptions\DomainException;
use App\Models\User;

class StartSubscriptionCheckout extends Action
{
    public function __construct(
        private readonly BillingGateway $billingGateway,
    ) {
    }

    public function execute(mixed ...$args): mixed
    {
        /** @var User $user */
        $user = $args[0];

        if ($user->activeSubscription()) {
            throw new DomainException(
                'You already have an active subscription.',
                'subscription_already_active',
            );
        }

        $priceId = (string) config('services.stripe.price_id');
        if (blank($priceId)) {
            throw new DomainException(
                'Stripe price is not configured. Set STRIPE_PRICE_ID in your environment.',
                'stripe_price_missing',
            );
        }

        $existingCustomerId = $user->subscriptions()
            ->whereNotNull('stripe_customer_id')
            ->latest('id')
            ->value('stripe_customer_id');

        return $this->billingGateway->createSubscriptionCheckout([
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'price_id' => $priceId,
            'customer_id' => $existingCustomerId,
            'success_url' => route('billing.success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('billing.cancel'),
        ]);
    }
}
