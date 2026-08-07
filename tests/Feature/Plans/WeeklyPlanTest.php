<?php

namespace Tests\Feature\Plans;

use App\Domains\Coaching\Contracts\AiCoachClient;
use App\Domains\Habits\Enums\HabitFrequency;
use App\Domains\Habits\Models\Habit;
use App\Domains\Identity\Enums\HealthFocusArea;
use App\Domains\Plans\Models\WeeklyPlan;
use App\Domains\Plans\Services\WeeklyPlanGenerator;
use App\Domains\Shared\Exceptions\DomainException;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WeeklyPlanTest extends TestCase
{
    use RefreshDatabase;

    private function onboardedUser(array $profile = []): User
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $user->profile()->create(array_merge([
            'focus_areas' => [HealthFocusArea::Sleep->value],
            'primary_goal' => 'Sleep consistently',
            'timezone' => 'UTC',
            'onboarding_completed_at' => now(),
        ], $profile));

        return $user;
    }

    public function test_user_can_generate_weekly_plan_from_valid_ai_json(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-08-10 12:00:00', 'UTC'));

        $this->mock(AiCoachClient::class, function ($mock) {
            $mock->shouldReceive('complete')
                ->once()
                ->andReturn(json_encode([
                    'title' => 'Sleep-focused week',
                    'summary' => 'Prioritize consistent rest and light recovery habits.',
                    'items' => [
                        [
                            'action' => 'Keep a fixed bedtime',
                            'category' => 'sleep',
                            'target' => 'In bed by 11pm on 5 nights',
                        ],
                        [
                            'action' => 'Morning sunlight walk',
                            'category' => 'fitness',
                            'target' => '10 minutes after waking on 4 days',
                        ],
                    ],
                ]));
        });

        $user = $this->onboardedUser();
        Habit::query()->create([
            'user_id' => $user->id,
            'name' => 'Consistent bedtime',
            'focus_area' => HealthFocusArea::Sleep,
            'frequency' => HabitFrequency::Daily,
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->post(route('plans.weekly.generate'))
            ->assertRedirect(route('plans.weekly.show'));

        $this->assertDatabaseCount('weekly_plans', 1);
        $plan = WeeklyPlan::query()->first();
        $this->assertSame('Sleep-focused week', $plan->title);
        $this->assertSame('2026-08-10', $plan->week_start->toDateString());
        $this->assertCount(2, $plan->items);

        Carbon::setTestNow();
    }

    public function test_invalid_ai_json_uses_fallback_and_upserts_same_week(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-08-10 12:00:00', 'UTC'));

        $this->mock(AiCoachClient::class, function ($mock) {
            $mock->shouldReceive('complete')
                ->twice()
                ->andReturn('not-json', '{"title":"","summary":"","items":[]}');
        });

        $user = $this->onboardedUser([
            'focus_areas' => [HealthFocusArea::Nutrition->value],
            'primary_goal' => 'Eat more whole foods',
        ]);

        $this->actingAs($user)->post(route('plans.weekly.generate'))->assertRedirect();
        $this->actingAs($user)->post(route('plans.weekly.generate'))->assertRedirect();

        $this->assertSame(1, WeeklyPlan::query()->where('user_id', $user->id)->count());
        $plan = WeeklyPlan::query()->first();
        $this->assertNotSame('', $plan->title);
        $this->assertNotEmpty($plan->items);
        $this->assertSame('2026-08-10', $plan->week_start->toDateString());

        Carbon::setTestNow();
    }

    public function test_user_without_habits_or_check_ins_can_generate_fallback_plan(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-08-10 12:00:00', 'UTC'));

        $this->mock(AiCoachClient::class, function ($mock) {
            $mock->shouldReceive('complete')
                ->once()
                ->andThrow(new DomainException('AI coach is not configured.', 'ai_not_configured'));
        });

        $user = $this->onboardedUser([
            'focus_areas' => [],
            'primary_goal' => null,
        ]);

        $this->actingAs($user)
            ->post(route('plans.weekly.generate'))
            ->assertRedirect(route('plans.weekly.show'));

        $plan = WeeklyPlan::query()->where('user_id', $user->id)->first();
        $this->assertNotNull($plan);
        $this->assertNotEmpty($plan->items);
        $this->assertGreaterThanOrEqual(1, count($plan->items));

        foreach ($plan->items as $item) {
            $this->assertArrayHasKey('action', $item);
            $this->assertArrayHasKey('category', $item);
            $this->assertArrayHasKey('target', $item);
            $this->assertContains($item['category'], ['sleep', 'nutrition', 'fitness', 'mindset', 'habit']);
        }

        $this->actingAs($user)
            ->get(route('plans.weekly.show'))
            ->assertOk()
            ->assertSee(WeeklyPlanGenerator::PLAN_DISCLAIMER)
            ->assertSee($plan->title);

        Carbon::setTestNow();
    }
}
