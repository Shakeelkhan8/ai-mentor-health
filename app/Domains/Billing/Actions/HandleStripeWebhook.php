<?php

namespace App\Domains\Billing\Actions;

use App\Domains\Billing\Services\SubscriptionSyncService;
use App\Domains\Shared\Actions\Action;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Applies a verified Stripe event. Stripe delivers at least once and in no guaranteed
 * order, so this is safe to run twice, concurrently, or out of order:
 *
 * - The event id is recorded in stripe_webhook_events inside the same transaction as
 *   the sync. A redelivery finds it and does nothing; a delivery that fails midway
 *   rolls the record back with everything else, so Stripe's retry is processed.
 *   insertOrIgnore (not catching a unique violation) keeps the transaction usable on
 *   PostgreSQL, and a concurrent duplicate waits on the unique index until the first
 *   delivery commits or rolls back.
 * - The event's creation time is handed to the sync, which ignores events older than
 *   the last one applied to that subscription.
 */
class HandleStripeWebhook extends Action
{
    public function __construct(
        private readonly SubscriptionSyncService $syncService,
    ) {
    }

    public function execute(mixed ...$args): mixed
    {
        /** @var array{id?: string, type: string, created?: int, data: array} $event */
        $event = $args[0];
        $eventId = (string) ($event['id'] ?? '');
        $type = (string) ($event['type'] ?? '');
        $object = (array) data_get($event, 'data.object', []);
        $eventAt = ! empty($event['created']) ? Carbon::createFromTimestamp((int) $event['created']) : null;

        return DB::transaction(function () use ($eventId, $type, $object, $eventAt) {
            if ($eventId !== '' && ! $this->recordFirstDelivery($eventId, $type)) {
                Log::info('Stripe webhook already processed', ['event_id' => $eventId, 'type' => $type]);

                return null;
            }

            $subscription = match ($type) {
                'checkout.session.completed' => $this->syncService->syncFromCheckoutSession($object, $eventAt),
                'customer.subscription.created',
                'customer.subscription.updated',
                'customer.subscription.deleted' => $this->syncService->syncFromStripeSubscription($object, $eventAt),
                default => null,
            };

            if ($subscription === null && $type !== '') {
                Log::info('Stripe webhook ignored or unmatched', [
                    'type' => $type,
                    'event_id' => $eventId ?: null,
                ]);
            }

            return $subscription;
        });
    }

    private function recordFirstDelivery(string $eventId, string $type): bool
    {
        return DB::table('stripe_webhook_events')->insertOrIgnore([
            'stripe_event_id' => $eventId,
            'type' => $type,
            'processed_at' => now(),
        ]) === 1;
    }
}
