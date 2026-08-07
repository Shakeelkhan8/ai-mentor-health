<?php

namespace Tests\Feature\Habits;

use App\Domains\Habits\Enums\CheckInStatus;
use App\Domains\Habits\Enums\HabitFrequency;
use App\Domains\Habits\Models\Habit;
use App\Domains\Habits\Models\HabitCheckIn;
use App\Domains\Identity\Enums\HealthFocusArea;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HabitDashboardTest extends TestCase
{
    use RefreshDatabase;

    private function onboardedUser(): User
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $user->profile()->create([
            'focus_areas' => [HealthFocusArea::Sleep->value],
            'primary_goal' => 'Sleep consistently',
            'timezone' => 'UTC',
            'onboarding_completed_at' => now(),
        ]);

        return $user;
    }

    public function test_dashboard_shows_habits_for_onboarded_user(): void
    {
        $user = $this->onboardedUser();

        Habit::query()->create([
            'user_id' => $user->id,
            'name' => 'Consistent bedtime',
            'focus_area' => HealthFocusArea::Sleep,
            'frequency' => HabitFrequency::Daily,
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Consistent bedtime')
            ->assertSee('Today’s habits');
    }

    public function test_user_can_record_habit_check_in_for_today(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-08-07 10:00:00', 'UTC'));

        $user = $this->onboardedUser();
        $habit = Habit::query()->create([
            'user_id' => $user->id,
            'name' => 'Drink water',
            'focus_area' => HealthFocusArea::Nutrition,
            'frequency' => HabitFrequency::Daily,
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->post(route('habits.check-ins.store', $habit), [
                'status' => CheckInStatus::Done->value,
            ])
            ->assertRedirect();

        $this->assertTrue(
            HabitCheckIn::query()
                ->where('habit_id', $habit->id)
                ->where('user_id', $user->id)
                ->whereDate('check_in_date', '2026-08-07')
                ->where('status', CheckInStatus::Done->value)
                ->exists()
        );

        // Upsert same day
        $this->actingAs($user)
            ->post(route('habits.check-ins.store', $habit), [
                'status' => CheckInStatus::Skipped->value,
            ])
            ->assertRedirect();

        $this->assertSame(1, HabitCheckIn::query()->where('habit_id', $habit->id)->count());
        $this->assertSame(
            CheckInStatus::Skipped,
            HabitCheckIn::query()->where('habit_id', $habit->id)->first()->status
        );

        Carbon::setTestNow();
    }

    public function test_dashboard_shows_done_badge_after_check_in(): void
    {
        $user = $this->onboardedUser();
        $habit = Habit::query()->create([
            'user_id' => $user->id,
            'name' => 'Drink water',
            'focus_area' => HealthFocusArea::Nutrition,
            'frequency' => HabitFrequency::Daily,
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->post(route('habits.check-ins.store', $habit), [
                'status' => CheckInStatus::Done->value,
            ])
            ->assertRedirect();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Done today');
    }

    public function test_user_cannot_check_in_another_users_habit(): void
    {
        $user = $this->onboardedUser();
        $other = $this->onboardedUser();

        $habit = Habit::query()->create([
            'user_id' => $other->id,
            'name' => 'Other habit',
            'focus_area' => HealthFocusArea::Fitness,
            'frequency' => HabitFrequency::Daily,
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->post(route('habits.check-ins.store', $habit), [
                'status' => CheckInStatus::Done->value,
            ])
            ->assertNotFound();
    }
}
