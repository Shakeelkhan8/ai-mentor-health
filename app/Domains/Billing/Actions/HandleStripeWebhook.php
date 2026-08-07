<?php

namespace App\Domains\Billing\Actions;

use App\Domains\Billing\Services\SubscriptionSyncService;
use App\Domains\Shared\Actions\Action;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HandleStripeWebhook extends Action
{
    public function __construct(
        private readonly SubscriptionSyncService $syncService,
    ) {
    }

    public function execute(mixed ...$args): mixed
    {
        /** @var array{id?: string, type: string, data: array} $event */
        $event = $args[0];
        $type = (string) ($event['type'] ?? '');
        $object = (array) data_get($event, 'data.object', []);

        return DB::transaction(function () use ($type, $object, $event) {
            $subscription = match ($type) {
                'checkout.session.completed' => $this->syncService->syncFromCheckoutSession($object),
                'customer.subscription.created',
                'customer.subscription.updated',
                'customer.subscription.deleted' => $this->syncService->syncFromStripeSubscription($object),
                default => null,
            };

            if ($subscription === null && $type !== '') {
                Log::info('Stripe webhook ignored or unmatched', [
                    'type' => $type,
                    'event_id' => $event['id'] ?? null,
                ]);
            }

            return $subscription;
        });
    }
}
