<?php

namespace App\Domains\Plans\Actions;

use App\Domains\Plans\Enums\PlanStatus;
use App\Domains\Plans\Models\WeeklyPlan;
use App\Domains\Plans\Services\WeeklyPlanGenerator;
use App\Domains\Shared\Actions\Action;
use App\Domains\Shared\Exceptions\DomainException;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

class GenerateWeeklyPlan extends Action
{
    public function __construct(
        private readonly WeeklyPlanGenerator $generator,
    ) {
    }

    public function execute(mixed ...$args): mixed
    {
        /** @var User $user */
        $user = $args[0];

        if (! $user->profile?->hasCompletedOnboarding()) {
            throw new DomainException('Complete onboarding before generating a weekly plan.', 'onboarding_required');
        }

        $timezone = $user->profile->timezone ?: config('app.timezone', 'UTC');
        $weekStart = Carbon::now($timezone)->startOfWeek(Carbon::MONDAY)->toDateString();

        return DB::transaction(function () use ($user, $weekStart) {
            $existing = WeeklyPlan::query()
                ->where('user_id', $user->id)
                ->whereDate('week_start', $weekStart)
                ->first();

            $payload = $this->generator->generate($user, $weekStart, $existing);

            $attributes = [
                'status' => PlanStatus::Active,
                'title' => $payload['title'],
                'summary' => $payload['summary'],
                'items' => $payload['items'],
                'generated_at' => now(),
                'week_start' => $weekStart,
            ];

            if ($existing) {
                $existing->fill($attributes)->save();
                $plan = $existing->fresh();
            } else {
                try {
                    $plan = WeeklyPlan::query()->create([
                        'user_id' => $user->id,
                        ...$attributes,
                    ]);
                } catch (UniqueConstraintViolationException) {
                    $plan = WeeklyPlan::query()
                        ->where('user_id', $user->id)
                        ->whereDate('week_start', $weekStart)
                        ->firstOrFail();
                    $plan->fill($attributes)->save();
                    $plan = $plan->fresh();
                }
            }

            $plan->setAttribute('generation_source', $payload['source']);

            return $plan;
        });
    }
}
