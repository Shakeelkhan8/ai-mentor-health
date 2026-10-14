<?php

namespace Tests\Feature;

use App\Domains\Coaching\Contracts\AiCoachClient;
use App\Domains\Coaching\Models\CoachConversation;
use App\Domains\Habits\Models\Habit;
use App\Domains\Habits\Models\HabitCheckIn;
use App\Domains\Plans\Models\WeeklyPlan;
use App\Infrastructure\Ai\DemoCoachClient;
use App\Models\User;
use Database\Seeders\DemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoModeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'mentor.demo.enabled' => true,
            // No AI key in the demo: any real provider call would fail with ai_not_configured.
            'services.rapidapi.key' => null,
        ]);
    }

    private function seedDemo(): User
    {
        $this->seed(DemoSeeder::class);

        return User::query()->where('email', config('mentor.demo.email'))->sole();
    }

    public function test_demo_seeder_creates_a_ready_to_explore_account(): void
    {
        $demo = $this->seedDemo();

        $this->assertTrue($demo->profile->hasCompletedOnboarding());
        $this->assertNotNull($demo->activeSubscription(), 'The shared demo account is Pro, so visitors never hit the free daily limit.');
        $this->assertGreaterThan(0, Habit::query()->where('user_id', $demo->id)->count());
        $this->assertGreaterThan(0, HabitCheckIn::query()->where('user_id', $demo->id)->count());
        $this->assertSame(1, WeeklyPlan::query()->where('user_id', $demo->id)->count());
        $this->assertGreaterThan(0, CoachConversation::query()->where('user_id', $demo->id)->sole()->messages()->count());
    }

    public function test_demo_seeder_can_run_on_every_boot(): void
    {
        $this->seedDemo();
        $counts = [User::query()->count(), Habit::query()->count(), HabitCheckIn::query()->count(), WeeklyPlan::query()->count()];

        $this->seedDemo();

        $this->assertSame($counts, [User::query()->count(), Habit::query()->count(), HabitCheckIn::query()->count(), WeeklyPlan::query()->count()]);
    }

    public function test_try_the_demo_logs_in_to_the_shared_account(): void
    {
        $demo = $this->seedDemo();

        $this->post(route('demo.login'))->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($demo);

        $this->get(route('dashboard'))->assertOk();
    }

    public function test_demo_login_does_not_exist_when_demo_mode_is_off(): void
    {
        $this->seedDemo();
        config(['mentor.demo.enabled' => false]);

        $this->post(route('demo.login'))->assertNotFound();
        $this->assertGuest();
    }

    public function test_login_page_offers_the_demo_only_in_demo_mode(): void
    {
        $this->get('/login')->assertOk()->assertSee('Try the demo');

        config(['mentor.demo.enabled' => false]);

        $this->get('/login')->assertOk()->assertDontSee('Try the demo');
    }

    public function test_login_page_has_a_single_well_formed_form_with_remember_me(): void
    {
        config(['mentor.demo.enabled' => false]);

        $html = $this->get('/login')->assertOk()->getContent();

        $this->assertSame(1, substr_count($html, '<form'));
        $this->assertStringContainsString('name="remember"', $html);
    }

    public function test_shared_demo_account_cannot_be_changed_or_deleted(): void
    {
        $demo = $this->seedDemo();
        $passwordHash = $demo->password;

        $this->actingAs($demo);

        $this->from('/edit-profile')->post(route('update-password'), [
            'currentPassword' => 'x', 'newPassword' => 'new-password', 'newPassword_confirmation' => 'new-password',
        ])->assertRedirect('/edit-profile')->assertSessionHas('error');

        $this->from('/edit-profile')->post(route('profile.update'), ['name' => 'Hacked', 'email' => 'hacked@example.com'])
            ->assertRedirect('/edit-profile')->assertSessionHas('error');

        $this->from('/profile')->delete(route('profile.destroy'), ['password' => 'anything'])
            ->assertRedirect('/profile')->assertSessionHas('error');

        $demo->refresh();
        $this->assertSame($passwordHash, $demo->password);
        $this->assertSame(config('mentor.demo.email'), $demo->email);
        $this->assertNotSame('Hacked', $demo->name);
    }

    public function test_visitors_own_accounts_are_not_restricted(): void
    {
        $this->seedDemo();
        $user = User::factory()->create();
        $user->profile()->create([
            'focus_areas' => ['sleep'], 'primary_goal' => 'Sleep', 'timezone' => 'UTC', 'onboarding_completed_at' => now(),
        ]);

        $this->actingAs($user)->delete(route('profile.destroy'), ['password' => 'password'])->assertRedirect('/');

        $this->assertNull($user->fresh());
    }

    public function test_coach_uses_prewritten_replies_and_says_so(): void
    {
        $demo = $this->seedDemo();

        $this->assertInstanceOf(DemoCoachClient::class, app(AiCoachClient::class));

        $this->actingAs($demo)
            ->postJson(route('coach.messages.store'), ['message' => 'How can I sleep better?'])
            ->assertOk()
            ->assertJsonPath('message', fn ($reply) => is_string($reply) && str_contains(strtolower($reply), 'sleep'));

        $this->actingAs($demo)->get(route('coach.index'))->assertOk()->assertSee('Demo mode');
    }
}
