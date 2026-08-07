<?php

namespace App\Domains\Identity\Actions;

use App\Domains\Habits\Enums\HabitFrequency;
use App\Domains\Habits\Models\Habit;
use App\Domains\Identity\Enums\HealthFocusArea;
use App\Domains\Identity\Models\UserProfile;
use App\Domains\Shared\Actions\Action;
use App\Domains\Shared\Exceptions\DomainException;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CompleteOnboarding extends Action
{
    /**
     * @param  array{focus_areas: array<int, string>, primary_goal: string, timezone?: string|null}  $payload
     * @return array{profile: UserProfile, habits_created: int}
     */
    public function execute(mixed ...$args): mixed
    {
        /** @var User $user */
        $user = $args[0];
        /** @var array $payload */
        $payload = $args[1];

        $focusAreas = array_values(array_unique($payload['focus_areas'] ?? []));
        $primaryGoal = trim((string) ($payload['primary_goal'] ?? ''));
        $timezone = trim((string) ($payload['timezone'] ?? config('app.timezone', 'UTC')));

        if ($focusAreas === []) {
            throw new DomainException('Select at least one focus area.', 'onboarding_focus_required');
        }

        foreach ($focusAreas as $area) {
            if (HealthFocusArea::tryFrom($area) === null) {
                throw new DomainException("Invalid focus area: {$area}", 'onboarding_invalid_focus');
            }
        }

        if ($primaryGoal === '' || mb_strlen($primaryGoal) > 500) {
            throw new DomainException('Please share a clear primary goal (max 500 characters).', 'onboarding_goal_invalid');
        }

        if ($timezone === '' || ! in_array($timezone, timezone_identifiers_list(), true)) {
            $timezone = 'UTC';
        }

        return DB::transaction(function () use ($user, $focusAreas, $primaryGoal, $timezone) {
            /** @var UserProfile $profile */
            $profile = UserProfile::query()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'focus_areas' => $focusAreas,
                    'primary_goal' => $primaryGoal,
                    'timezone' => $timezone,
                    'onboarding_completed_at' => now(),
                ]
            );

            $habitsCreated = 0;

            if ($user->habits()->doesntExist()) {
                foreach ($this->starterHabitsFor($focusAreas) as $index => $habit) {
                    Habit::query()->create([
                        'user_id' => $user->id,
                        'name' => $habit['name'],
                        'description' => $habit['description'],
                        'focus_area' => $habit['focus_area'],
                        'frequency' => HabitFrequency::Daily,
                        'target_per_period' => 1,
                        'is_active' => true,
                        'sort_order' => $index,
                    ]);
                    $habitsCreated++;
                }
            }

            return [
                'profile' => $profile->fresh(),
                'habits_created' => $habitsCreated,
            ];
        });
    }

    /**
     * @param  array<int, string>  $focusAreas
     * @return array<int, array{name: string, description: string, focus_area: string}>
     */
    private function starterHabitsFor(array $focusAreas): array
    {
        $catalog = [
            HealthFocusArea::Fitness->value => [
                'name' => 'Move for 20 minutes',
                'description' => 'Walk, stretch, or light exercise.',
            ],
            HealthFocusArea::Nutrition->value => [
                'name' => 'Drink water intentionally',
                'description' => 'Hit a simple hydration target today.',
            ],
            HealthFocusArea::Sleep->value => [
                'name' => 'Keep a consistent bedtime',
                'description' => 'Wind down and sleep at a steady time.',
            ],
            HealthFocusArea::Stress->value => [
                'name' => 'Take a 5-minute reset',
                'description' => 'Breathing, journaling, or quiet time.',
            ],
            HealthFocusArea::Preventive->value => [
                'name' => 'Log how you feel',
                'description' => 'Note energy and mood once per day.',
            ],
        ];

        $habits = [];
        foreach ($focusAreas as $area) {
            if (! isset($catalog[$area])) {
                continue;
            }
            $habits[] = [
                'name' => $catalog[$area]['name'],
                'description' => $catalog[$area]['description'],
                'focus_area' => $area,
            ];
        }

        return $habits;
    }
}
