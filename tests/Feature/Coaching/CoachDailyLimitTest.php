<?php

namespace Tests\Feature\Coaching;

use App\Domains\Billing\Enums\SubscriptionStatus;
use App\Domains\Billing\Models\Subscription;
use App\Domains\Coaching\Contracts\AiCoachClient;
use App\Domains\Identity\Enums\HealthFocusArea;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoachDailyLimitTest extends TestCase
{
    use RefreshDatabase;

    private int $aiCalls = 0;

    protected function setUp(): void
    {
        parent::setUp();

        config(['mentor.limits.free_coach_messages_per_day' => 3]);

        $this->mock(AiCoachClient::class, function ($mock) {
            $mock->shouldReceive('complete')->andReturnUsing(function () {
                $this->aiCalls++;

                return 'Coach reply';
            });
        });
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    private function onboardedUser(string $timezone = 'UTC'): User
    {
        $user = User::factory()->create();

        $user->profile()->create([
            'focus_areas' => [HealthFocusArea::Sleep->value],
            'primary_goal' => 'Sleep better',
            'timezone' => $timezone,
            'onboarding_completed_at' => now(),
        ]);

        return $user;
    }

    private function makePro(User $user): void
    {
        Subscription::query()->create([
            'user_id' => $user->id,
            'status' => SubscriptionStatus::Active,
            'stripe_subscription_id' => 'sub_'.$user->id,
        ]);
    }

    private function send(User $user): \Illuminate\Testing\TestResponse
    {
        return $this->actingAs($user)->postJson(route('coach.messages.store'), ['message' => 'Hello coach']);
    }

    public function test_free_user_is_stopped_after_the_daily_limit(): void
    {
        $user = $this->onboardedUser();

        for ($i = 1; $i <= 3; $i++) {
            $this->send($user)->assertOk()->assertJsonPath('remaining_today', 3 - $i);
        }

        $this->send($user)
            ->assertStatus(429)
            ->assertJsonPath('code', 'coach_daily_limit_reached')
            ->assertJsonPath('upgrade_url', route('billing.show'));

        // The blocked message never reached the AI provider.
        $this->assertSame(3, $this->aiCalls);
    }

    public function test_pro_user_has_no_daily_limit(): void
    {
        $user = $this->onboardedUser();
        $this->makePro($user);

        for ($i = 1; $i <= 4; $i++) {
            $this->send($user)->assertOk()->assertJsonPath('remaining_today', null);
        }

        $this->assertSame(4, $this->aiCalls);
    }

    public function test_limit_resets_at_midnight_in_the_users_timezone(): void
    {
        config(['mentor.limits.free_coach_messages_per_day' => 1]);
        $user = $this->onboardedUser('Asia/Karachi'); // UTC+5

        // 18:30 UTC = 23:30 on the 14th in Karachi.
        Carbon::setTestNow(Carbon::parse('2026-10-14 18:30:00', 'UTC'));
        $this->send($user)->assertOk();

        // 19:30 UTC is still the 14th in UTC, but already the 15th in Karachi.
        Carbon::setTestNow(Carbon::parse('2026-10-14 19:30:00', 'UTC'));
        $this->send($user)->assertOk();
    }

    public function test_no_limit_when_subscriptions_are_disabled(): void
    {
        config(['mentor.features.subscriptions' => false]);
        $user = $this->onboardedUser();

        for ($i = 1; $i <= 4; $i++) {
            $this->send($user)->assertOk();
        }

        $this->assertSame(4, $this->aiCalls);
    }

    public function test_coach_page_shows_free_messages_left_today(): void
    {
        $user = $this->onboardedUser();
        $this->send($user)->assertOk();

        $this->actingAs($user)
            ->get(route('coach.index'))
            ->assertOk()
            ->assertSeeText('2 of 3 free messages left today');
    }
}
