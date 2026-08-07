<?php

namespace App\Domains\Habits\Actions;

use App\Domains\Habits\Enums\CheckInStatus;
use App\Domains\Habits\Models\Habit;
use App\Domains\Habits\Models\HabitCheckIn;
use App\Domains\Shared\Actions\Action;
use App\Domains\Shared\Exceptions\DomainException;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

class RecordHabitCheckIn extends Action
{
    public function execute(mixed ...$args): mixed
    {
        /** @var User $user */
        $user = $args[0];
        /** @var Habit|int $habit */
        $habit = $args[1];
        /** @var array $payload */
        $payload = $args[2];

        if (! $habit instanceof Habit) {
            $habit = Habit::query()->findOrFail($habit);
        }

        if ((int) $habit->user_id !== (int) $user->id) {
            throw new DomainException('Habit not found.', 'habit_not_found');
        }

        if (! $habit->is_active) {
            throw new DomainException('This habit is inactive.', 'habit_inactive');
        }

        $status = CheckInStatus::tryFrom((string) ($payload['status'] ?? ''));
        if ($status === null) {
            throw new DomainException('Invalid check-in status.', 'invalid_check_in_status');
        }

        $timezone = $user->profile?->timezone ?: config('app.timezone', 'UTC');
        $today = Carbon::now($timezone)->toDateString();

        return DB::transaction(function () use ($user, $habit, $status, $payload, $today) {
            $checkIn = HabitCheckIn::query()
                ->where('habit_id', $habit->id)
                ->whereDate('check_in_date', $today)
                ->first();

            $attributes = [
                'user_id' => $user->id,
                'status' => $status,
                'note' => isset($payload['note']) ? trim((string) $payload['note']) : null,
                'mood_score' => $payload['mood_score'] ?? null,
                'check_in_date' => $today,
            ];

            if ($checkIn) {
                $checkIn->fill($attributes)->save();
            } else {
                try {
                    $checkIn = HabitCheckIn::query()->create([
                        'habit_id' => $habit->id,
                        ...$attributes,
                    ]);
                } catch (UniqueConstraintViolationException) {
                    $checkIn = HabitCheckIn::query()
                        ->where('habit_id', $habit->id)
                        ->whereDate('check_in_date', $today)
                        ->firstOrFail();
                    $checkIn->fill($attributes)->save();
                }
            }

            return $checkIn->fresh(['habit']);
        });
    }
}