<?php

namespace Tests\Feature\Identity;

use App\Domains\Habits\Models\Habit;
use App\Domains\Identity\Enums\HealthFocusArea;
use App\Domains\Identity\Models\UserProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OnboardingTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_is_redirected_to_onboarding_until_complete(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('onboarding.show'));
    }

    public function test_user_can_complete_onboarding_and_gets_starter_habits(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->post(route('onboarding.store'), [
            'focus_areas' => [
                HealthFocusArea::Sleep->value,
                HealthFocusArea::Fitness->value,
            ],
            'primary_goal' => 'Sleep better and move daily.',
            'timezone' => 'Asia/Karachi',
        ]);

        $response->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('user_profiles', [
            'user_id' => $user->id,
            'timezone' => 'Asia/Karachi',
        ]);

        $profile = UserProfile::query()->where('user_id', $user->id)->first();
        $this->assertNotNull($profile?->onboarding_completed_at);
        $this->assertEqualsCanonicalizing(
            [HealthFocusArea::Sleep->value, HealthFocusArea::Fitness->value],
            $profile->focus_areas
        );

        $this->assertSame(2, Habit::query()->where('user_id', $user->id)->count());

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk();
    }

    public function test_onboarding_requires_focus_areas(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user)
            ->post(route('onboarding.store'), [
                'focus_areas' => [],
                'primary_goal' => 'Improve my habits',
            ])
            ->assertSessionHasErrors('focus_areas');
    }
}
