<?php

namespace Tests\Feature\Habits;

use App\Domains\Habits\Enums\CheckInStatus;
use App\Domains\Habits\Enums\HabitFrequency;
use App\Domains\Habits\Models\Habit;
use App\Domains\Habits\Models\HabitCheckIn;
use App\Domains\Identity\Enums\HealthFocusArea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HabitDashboardSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_check_in_works(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $user->profile()->create([
            'focus_areas' => [HealthFocusArea::Sleep->value],
            'primary_goal' => 'Sleep',
            'timezone' => 'UTC',
            'onboarding_completed_at' => now(),
        ]);

        $habit = Habit::query()->create([
            'user_id' => $user->id,
            'name' => 'Bedtime',
            'focus_area' => HealthFocusArea::Sleep,
            'frequency' => HabitFrequency::Daily,
            'is_active' => true,
        ]);

        $this->actingAs($user)->get(route('dashboard'))->assertOk()->assertSee('Bedtime');

        $this->actingAs($user)
            ->post(route('habits.check-ins.store', $habit), ['status' => CheckInStatus::Done->value])
            ->assertRedirect();

        $this->assertTrue(
            HabitCheckIn::query()->where('habit_id', $habit->id)->where('status', CheckInStatus::Done->value)->exists()
        );
    }
}