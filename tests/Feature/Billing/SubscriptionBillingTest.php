<?php

namespace Tests\Feature\Billing;

use App\Domains\Billing\Contracts\BillingGateway;
use App\Domains\Billing\Enums\SubscriptionStatus;
use App\Domains\Billing\Models\Subscription;
use App\Domains\Identity\Enums\HealthFocusArea;
use App\Domains\Shared\Exceptions\DomainException;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionBillingTest extends TestCase
{
    use RefreshDatabase;

    private function onboardedUser(): User
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $user->profile()->create([
            'focus_areas' => [HealthFocusArea::Sleep->value],
            'primary_goal' => 'Sleep better',
            'timezone' => 'UTC',
            'onboarding_completed_at' => now(),
        ]);

        return $user;
    }

    public function test_user_can_start_checkout_and_is_redirected_to_stripe(): void
    {
        $this->mock(BillingGateway::class, function ($mock) {
            $mock->shouldReceive('createSubscriptionCheckout')
                ->once()
                ->andReturn([
                    'id' => 'cs_test_123',
                    'url' => 'https://checkout.stripe.com/c/pay/cs_test_123',
                ]);
        });

        $user = $this->onboardedUser();

        $this->actingAs($user)
            ->post(route('billing.checkout'))
            ->assertRedirect('https://checkout.stripe.com/c/pay/cs_test_123');
    }

    public function test_checkout_blocked_when_already_subscribed(): void
    {
        $user = $this->onboardedUser();

        Subscription::query()->create([
            'user_id' => $user->id,
            'status' => SubscriptionStatus::Active,
            'stripe_subscription_id' => 'sub_existing',
            'stripe_customer_id' => 'cus_existing',
            'stripe_price_id' => 'price_test_mentor_pro',
        ]);

        $this->mock(BillingGateway::class, function ($mock) {
            $mock->shouldNotReceive('createSubscriptionCheckout');
        });

        $this->actingAs($user)
            ->from(route('billing.show'))
            ->post(route('billing.checkout'))
            ->assertRedirect(route('billing.show'))
            ->assertSessionHasErrors('billing');
    }

    public function test_webhook_checkout_session_completed_creates_subscription(): void
    {
        $user = $this->onboardedUser();

        $payload = [
            'id' => 'evt_test_1',
            'type' => 'checkout.session.completed',
            'data' => [
                    'object' => [
                    'id' => 'cs_test_1',
                    'mode' => 'subscription',
                    'payment_status' => 'paid',
                    'client_reference_id' => (string) $user->id,
                    'customer' => 'cus_test_1',
                    'subscription' => 'sub_test_1',
                    'metadata' => [
                        'user_id' => (string) $user->id,
                        'price_id' => 'price_test_mentor_pro',
                    ],
                ],
            ],
        ];

        $this->mock(BillingGateway::class, function ($mock) use ($payload) {
            $mock->shouldReceive('parseWebhook')
                ->once()
                ->andReturn($payload);
        });

        $this->postJson(route('stripe.webhook'), $payload)
            ->assertOk()
            ->assertJson(['received' => true]);

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $user->id,
            'stripe_subscription_id' => 'sub_test_1',
            'stripe_customer_id' => 'cus_test_1',
            'status' => SubscriptionStatus::Active->value,
        ]);
    }

    public function test_webhook_subscription_updated_syncs_status(): void
    {
        $user = $this->onboardedUser();

        Subscription::query()->create([
            'user_id' => $user->id,
            'status' => SubscriptionStatus::Active,
            'stripe_subscription_id' => 'sub_test_2',
            'stripe_customer_id' => 'cus_test_2',
            'stripe_price_id' => 'price_test_mentor_pro',
        ]);

        $payload = [
            'id' => 'evt_test_2',
            'type' => 'customer.subscription.updated',
            'data' => [
                'object' => [
                    'id' => 'sub_test_2',
                    'status' => 'canceled',
                    'customer' => 'cus_test_2',
                    'canceled_at' => 1780000000,
                    'current_period_start' => 1779000000,
                    'current_period_end' => 1780000000,
                    'metadata' => [
                        'user_id' => (string) $user->id,
                    ],
                    'items' => [
                        'data' => [
                            ['price' => ['id' => 'price_test_mentor_pro']],
                        ],
                    ],
                ],
            ],
        ];

        $this->mock(BillingGateway::class, function ($mock) use ($payload) {
            $mock->shouldReceive('parseWebhook')
                ->once()
                ->andReturn($payload);
        });

        $this->postJson(route('stripe.webhook'), $payload)->assertOk();

        $this->assertSame(
            SubscriptionStatus::Canceled,
            Subscription::query()->where('stripe_subscription_id', 'sub_test_2')->first()->status
        );
    }

    public function test_checkout_surfaces_gateway_configuration_errors(): void
    {
        $this->mock(BillingGateway::class, function ($mock) {
            $mock->shouldReceive('createSubscriptionCheckout')
                ->once()
                ->andThrow(new DomainException('Stripe is not configured.', 'stripe_not_configured'));
        });

        $user = $this->onboardedUser();

        $this->actingAs($user)
            ->from(route('billing.show'))
            ->post(route('billing.checkout'))
            ->assertRedirect(route('billing.show'))
            ->assertSessionHasErrors('billing');
    }
}
