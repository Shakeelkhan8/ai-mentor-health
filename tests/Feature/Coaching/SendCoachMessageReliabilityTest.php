<?php

namespace Tests\Feature\Coaching;

use App\Domains\Coaching\Contracts\AiCoachClient;
use App\Domains\Identity\Enums\HealthFocusArea;
use App\Domains\Shared\Exceptions\DomainException;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class SendCoachMessageReliabilityTest extends TestCase
{
    use RefreshDatabase;

    private function onboardedUser(): User
    {
        $user = User::factory()->create();

        $user->profile()->create([
            'focus_areas' => [HealthFocusArea::Sleep->value],
            'primary_goal' => 'Sleep better',
            'timezone' => 'UTC',
            'onboarding_completed_at' => now(),
        ]);

        return $user;
    }

    public function test_ai_call_runs_outside_a_database_transaction(): void
    {
        $levels = [];

        $this->mock(AiCoachClient::class, function ($mock) use (&$levels) {
            $mock->shouldReceive('complete')->andReturnUsing(function () use (&$levels) {
                $levels[] = DB::transactionLevel();

                return 'Coach reply';
            });
        });

        // RefreshDatabase wraps each test in one transaction; anything above that is ours.
        $baseline = DB::transactionLevel();

        $this->actingAs($this->onboardedUser())
            ->postJson(route('coach.messages.store'), ['message' => 'Hello'])
            ->assertOk();

        $this->assertSame([$baseline], $levels);
        $this->assertDatabaseCount('coach_messages', 2);
    }

    public function test_failed_ai_reply_saves_nothing_and_uses_no_quota(): void
    {
        config(['mentor.limits.free_coach_messages_per_day' => 3]);

        $this->mock(AiCoachClient::class, function ($mock) {
            $mock->shouldReceive('complete')->andThrow(new DomainException('Unable to reach the AI coaching provider.', 'ai_provider_error'));
        });

        $user = $this->onboardedUser();

        $this->actingAs($user)
            ->postJson(route('coach.messages.store'), ['message' => 'Hello'])
            ->assertStatus(503)
            ->assertJsonPath('code', 'ai_provider_error')
            ->assertJsonPath('error', "Your coach is temporarily unavailable, so your message wasn't sent. Please try again in a minute.");

        $this->assertDatabaseCount('coach_messages', 0);

        $this->actingAs($user)
            ->get(route('coach.index'))
            ->assertSeeText('3 of 3 free messages left today');
    }

    public function test_provider_failure_is_logged_with_user_and_conversation(): void
    {
        $this->mock(AiCoachClient::class, function ($mock) {
            $mock->shouldReceive('complete')->andThrow(new DomainException('Unable to reach the AI coaching provider.', 'ai_provider_error'));
        });

        Log::spy();
        $user = $this->onboardedUser();

        $this->actingAs($user)->postJson(route('coach.messages.store'), ['message' => 'Hello'])->assertStatus(503);

        Log::shouldHaveReceived('error')
            ->withArgs(fn ($message, $context) => $message === 'AI coach reply failed'
                && $context['user_id'] === $user->id
                && array_key_exists('conversation_id', $context)
                && $context['code'] === 'ai_provider_error')
            ->once();
    }
}
