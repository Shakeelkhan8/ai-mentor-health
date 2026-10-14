<?php

namespace Database\Seeders;

use App\Domains\Billing\Enums\SubscriptionStatus;
use App\Domains\Billing\Models\Subscription;
use App\Domains\Coaching\Enums\MessageRole;
use App\Domains\Coaching\Models\CoachConversation;
use App\Domains\Habits\Enums\CheckInStatus;
use App\Domains\Habits\Models\Habit;
use App\Domains\Habits\Models\HabitCheckIn;
use App\Domains\Identity\Actions\CompleteOnboarding;
use App\Domains\Plans\Actions\GenerateWeeklyPlan;
use App\Infrastructure\Ai\DemoCoachClient;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * The shared "Try the demo" account: onboarded, Pro (so visitors never hit the free
 * daily limit), two weeks of habit check-ins, this week's plan and a short coach chat.
 *
 * Runs on every container boot (docker/start.sh) and does nothing if the account
 * already exists. Uses no factories: Faker is a dev dependency and isn't installed
 * in the production image. The account's password is random and never shown; the
 * only way in is the "Try the demo" button.
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('mentor.demo.email');

        if (User::query()->where('email', $email)->exists()) {
            return;
        }

        DB::transaction(function () use ($email) {
            $user = new User([
                'name' => 'Demo User',
                'email' => $email,
                'password' => Hash::make(Str::random(40)),
                'role' => 'user',
            ]);
            $user->forceFill(['email_verified_at' => now()])->save();

            app(CompleteOnboarding::class)->execute($user, [
                'focus_areas' => ['sleep', 'stress', 'fitness'],
                'primary_goal' => 'Sleep 7+ hours and handle work stress better',
                'timezone' => 'UTC',
            ]);

            Subscription::query()->create([
                'user_id' => $user->id,
                'status' => SubscriptionStatus::Active,
                'stripe_subscription_id' => 'sub_demo',
                'current_period_starts_at' => now()->startOfMonth(),
                'current_period_ends_at' => now()->startOfMonth()->addMonth(),
            ]);

            $this->seedCheckIns($user);
            $this->seedConversation($user);
        });

        // Outside the transaction above: plan generation manages its own (see GenerateWeeklyPlan).
        $user = User::query()->where('email', $email)->sole();
        app(GenerateWeeklyPlan::class)->execute($user->fresh());
    }

    /**
     * Thirteen days of history, leaving today open so visitors can check in themselves.
     * A fixed pattern (mostly done, some partial or skipped) keeps streaks realistic.
     */
    private function seedCheckIns(User $user): void
    {
        $pattern = [CheckInStatus::Done, CheckInStatus::Done, CheckInStatus::Partial, CheckInStatus::Done, CheckInStatus::Skipped];

        Habit::query()->where('user_id', $user->id)->orderBy('id')->get()
            ->each(function (Habit $habit, int $habitIndex) use ($user, $pattern) {
                for ($daysAgo = 13; $daysAgo >= 1; $daysAgo--) {
                    HabitCheckIn::query()->create([
                        'habit_id' => $habit->id,
                        'user_id' => $user->id,
                        'check_in_date' => now()->subDays($daysAgo)->toDateString(),
                        'status' => $pattern[($daysAgo + $habitIndex) % count($pattern)],
                        'mood_score' => 6 + (($daysAgo + $habitIndex) % 4),
                    ]);
                }
            });
    }

    private function seedConversation(User $user): void
    {
        $coach = app(DemoCoachClient::class);
        $conversation = CoachConversation::query()->create([
            'user_id' => $user->id,
            'title' => 'Waking up tired',
            'is_active' => true,
            'last_message_at' => now()->subDay(),
        ]);

        foreach (['I keep waking up tired even after 7 hours. Any ideas?', 'Work stress is making it worse. What can I do during the day?'] as $question) {
            $conversation->messages()->create(['user_id' => $user->id, 'role' => MessageRole::User, 'content' => $question]);
            $conversation->messages()->create([
                'user_id' => $user->id,
                'role' => MessageRole::Assistant,
                'content' => $coach->complete([['role' => 'user', 'content' => $question]]),
            ]);
        }
    }
}
