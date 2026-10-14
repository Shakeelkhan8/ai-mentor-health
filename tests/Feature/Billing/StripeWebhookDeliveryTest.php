<?php

namespace Tests\Feature\Billing;

use App\Domains\Billing\Contracts\BillingGateway;
use App\Domains\Billing\Enums\SubscriptionStatus;
use App\Domains\Billing\Models\Subscription;
use App\Domains\Billing\Services\SubscriptionSyncService;
use App\Infrastructure\Billing\StripeBillingGateway;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

/**
 * Stripe delivers webhooks at least once and in no guaranteed order.
 */
class StripeWebhookDeliveryTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    /** @var list<array<string, mixed>> */
    private array $queue = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        // The real gateway verifies signatures; here it hands back the next queued event.
        $this->mock(BillingGateway::class, function ($mock) {
            $mock->shouldReceive('parseWebhook')->andReturnUsing(fn () => array_shift($this->queue));
        });
    }

    private function deliver(array $event): \Illuminate\Testing\TestResponse
    {
        $this->queue[] = $event;

        return $this->postJson(route('stripe.webhook'), $event);
    }

    private function checkoutCompleted(string $eventId, int $created): array
    {
        return [
            'id' => $eventId,
            'type' => 'checkout.session.completed',
            'created' => $created,
            'data' => ['object' => [
                'id' => 'cs_test_1',
                'mode' => 'subscription',
                'payment_status' => 'paid',
                'customer' => 'cus_test_1',
                'subscription' => 'sub_test_1',
                'metadata' => ['user_id' => (string) $this->user->id],
            ]],
        ];
    }

    private function subscriptionEvent(string $eventId, string $type, string $status, int $created): array
    {
        return [
            'id' => $eventId,
            'type' => $type,
            'created' => $created,
            'data' => ['object' => [
                'id' => 'sub_test_1',
                'customer' => 'cus_test_1',
                'status' => $status,
                'metadata' => ['user_id' => (string) $this->user->id],
            ]],
        ];
    }

    private function subscriptionStatus(): SubscriptionStatus
    {
        return Subscription::query()->where('stripe_subscription_id', 'sub_test_1')->sole()->status;
    }

    public function test_retried_checkout_event_does_not_reactivate_a_canceled_subscription(): void
    {
        $this->deliver($this->checkoutCompleted('evt_checkout', 1_000))->assertOk();
        $this->deliver($this->subscriptionEvent('evt_deleted', 'customer.subscription.deleted', 'canceled', 2_000))->assertOk();

        // Stripe retries the original checkout event (same id) after the cancellation.
        $this->deliver($this->checkoutCompleted('evt_checkout', 1_000))->assertOk();

        $this->assertSame(SubscriptionStatus::Canceled, $this->subscriptionStatus());
    }

    public function test_redelivered_event_is_ignored_even_when_timestamps_tie(): void
    {
        // Same second: the ordering guard cannot tell these apart, only the event id can.
        $this->deliver($this->checkoutCompleted('evt_checkout', 1_000))->assertOk();
        $this->deliver($this->subscriptionEvent('evt_deleted', 'customer.subscription.deleted', 'canceled', 1_000))->assertOk();
        $this->deliver($this->checkoutCompleted('evt_checkout', 1_000))->assertOk();

        $this->assertSame(SubscriptionStatus::Canceled, $this->subscriptionStatus());
        $this->assertDatabaseCount('stripe_webhook_events', 2);
    }

    public function test_older_event_arriving_late_does_not_overwrite_newer_state(): void
    {
        $this->deliver($this->subscriptionEvent('evt_deleted', 'customer.subscription.deleted', 'canceled', 2_000))->assertOk();
        $this->deliver($this->subscriptionEvent('evt_updated', 'customer.subscription.updated', 'active', 1_000))->assertOk();

        $this->assertSame(SubscriptionStatus::Canceled, $this->subscriptionStatus());
    }

    public function test_newer_event_is_applied(): void
    {
        $this->deliver($this->subscriptionEvent('evt_updated', 'customer.subscription.updated', 'active', 1_000))->assertOk();
        $this->deliver($this->subscriptionEvent('evt_deleted', 'customer.subscription.deleted', 'canceled', 2_000))->assertOk();

        $this->assertSame(SubscriptionStatus::Canceled, $this->subscriptionStatus());
    }

    public function test_event_that_failed_mid_processing_is_processed_on_retry(): void
    {
        $real = app(SubscriptionSyncService::class);
        $calls = 0;

        $this->mock(SubscriptionSyncService::class, function ($mock) use ($real, &$calls) {
            $mock->shouldReceive('syncFromCheckoutSession')->andReturnUsing(function (...$args) use ($real, &$calls) {
                if (++$calls === 1) {
                    throw new RuntimeException('database went away');
                }

                return $real->syncFromCheckoutSession(...$args);
            });
        });

        $this->withoutExceptionHandling();

        try {
            $this->deliver($this->checkoutCompleted('evt_checkout', 1_000));
            $this->fail('The first delivery should have failed.');
        } catch (RuntimeException) {
            // Stripe sees a 500 and will retry.
        }

        $this->deliver($this->checkoutCompleted('evt_checkout', 1_000))->assertOk();

        $this->assertSame(2, $calls);
        $this->assertSame(SubscriptionStatus::Active, $this->subscriptionStatus());
    }

    public function test_gateway_passes_the_event_creation_time_through(): void
    {
        config(['services.stripe.webhook_secret' => null]);

        $event = (new StripeBillingGateway())->parseWebhook(
            json_encode(['id' => 'evt_1', 'type' => 'customer.subscription.updated', 'created' => 1_700_000_000, 'data' => []]),
            null,
        );

        $this->assertSame(1_700_000_000, $event['created']);
    }
}
