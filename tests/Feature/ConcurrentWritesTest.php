<?php

namespace Tests\Feature;

use App\Domains\Coaching\Contracts\AiCoachClient;
use App\Domains\Habits\Actions\RecordHabitCheckIn;
use App\Domains\Habits\Enums\CheckInStatus;
use App\Domains\Habits\Enums\HabitFrequency;
use App\Domains\Habits\Models\Habit;
use App\Domains\Habits\Models\HabitCheckIn;
use App\Domains\Identity\Enums\HealthFocusArea;
use App\Domains\Plans\Actions\GenerateWeeklyPlan;
use App\Domains\Plans\Models\WeeklyPlan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Two requests can both see "no row yet" and both insert; the loser hits the unique
 * index and falls back to updating the winner's row. On PostgreSQL a failed statement
 * aborts the whole transaction, so that fallback only works inside a savepoint.
 * These tests mean something on pgsql (CI runs them there); SQLite never aborts.
 */
class ConcurrentWritesTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

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

    /**
     * Right after the code's first lookup on $table finds nothing, run $insert as if a
     * concurrent request had just written the same row. It goes through the model so
     * dates are stored in the same format the app uses.
     */
    private function racingInsertAfterFirstLookup(string $table, \Closure $insert): void
    {
        $done = false;

        DB::listen(function (QueryExecuted $query) use ($table, $insert, &$done) {
            $sql = strtolower($query->sql);

            if (! $done && str_starts_with($sql, 'select') && str_contains($sql, $table)) {
                $done = true;
                $insert();
            }
        });
    }

    public function test_check_in_recovers_when_a_concurrent_request_inserts_the_same_day(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-14 09:00:00', 'UTC'));
        $user = $this->onboardedUser();
        $habit = Habit::query()->create([
            'user_id' => $user->id,
            'name' => 'Bedtime',
            'focus_area' => HealthFocusArea::Sleep,
            'frequency' => HabitFrequency::Daily,
            'is_active' => true,
        ]);

        $this->racingInsertAfterFirstLookup('habit_check_ins', fn () => HabitCheckIn::query()->create([
            'habit_id' => $habit->id,
            'user_id' => $user->id,
            'check_in_date' => '2026-10-14',
            'status' => CheckInStatus::Skipped,
        ]));

        $checkIn = app(RecordHabitCheckIn::class)->execute($user, $habit, ['status' => CheckInStatus::Done->value]);

        $this->assertSame(CheckInStatus::Done, $checkIn->status);
        $this->assertSame(1, HabitCheckIn::query()->count());
        $this->assertSame(CheckInStatus::Done, HabitCheckIn::query()->sole()->status);
    }

    public function test_weekly_plan_recovers_when_a_concurrent_request_inserts_the_same_week(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-14 09:00:00', 'UTC'));
        $user = $this->onboardedUser();

        $this->mock(AiCoachClient::class, function ($mock) {
            $mock->shouldReceive('complete')->andReturn(json_encode([
                'title' => 'Sleep-focused week',
                'summary' => 'Consistent rest.',
                'items' => [['action' => 'Fixed bedtime', 'category' => 'sleep', 'target' => '5 nights']],
            ]));
        });

        $this->racingInsertAfterFirstLookup('weekly_plans', fn () => WeeklyPlan::query()->create([
            'user_id' => $user->id,
            'week_start' => '2026-10-12',
            'status' => 'draft',
            'title' => 'Written by the other request',
        ]));

        $plan = app(GenerateWeeklyPlan::class)->execute($user);

        $this->assertSame('Sleep-focused week', $plan->title);
        $this->assertSame(1, WeeklyPlan::query()->count());
        $this->assertSame('Sleep-focused week', WeeklyPlan::query()->sole()->title);
    }
}
