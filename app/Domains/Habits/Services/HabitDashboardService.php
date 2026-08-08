<?php

namespace App\Domains\Habits\Services;

use App\Domains\Habits\Enums\CheckInStatus;
use App\Domains\Habits\Models\Habit;
use App\Domains\Habits\Models\HabitCheckIn;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class HabitDashboardService
{
    /**
     * @return array{
     *   timezone: string,
     *   today: string,
     *   habits: Collection,
     *   completed_today: int,
     *   total_active: int,
     *   completion_percent: int,
     *   week_done_count: int,
     *   longest_streak: int
     * }
     */
    public function forUser(User $user): array
    {
        $timezone = $user->profile?->timezone ?: config('app.timezone', 'UTC');
        $today = Carbon::now($timezone)->toDateString();
        $weekStart = Carbon::now($timezone)->startOfWeek()->toDateString();

        $habits = Habit::query()
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->with(['checkIns' => function ($query) use ($weekStart) {
                $query->whereDate('check_in_date', '>=', $weekStart)
                    ->orderByDesc('check_in_date');
            }])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $doneDatesByHabit = HabitCheckIn::query()
            ->where('user_id', $user->id)
            ->whereIn('habit_id', $habits->pluck('id'))
            ->where('status', CheckInStatus::Done)
            ->orderByDesc('check_in_date')
            ->get(['habit_id', 'check_in_date'])
            ->groupBy('habit_id')
            ->map(fn (Collection $rows) => $rows
                ->map(fn (HabitCheckIn $checkIn) => $checkIn->check_in_date->toDateString())
                ->unique()
                ->values());

        $habits = $habits->map(function (Habit $habit) use ($today, $timezone, $doneDatesByHabit) {
            $todayCheckIn = $habit->checkIns->first(
                fn (HabitCheckIn $checkIn) => $checkIn->check_in_date->toDateString() === $today
            );

            $habit->setAttribute('today_check_in', $todayCheckIn);
            $habit->setAttribute(
                'current_streak',
                $this->streakFromDates($doneDatesByHabit->get($habit->id, collect()), $timezone)
            );

            return $habit;
        });

        $completedToday = $habits->filter(
            fn (Habit $habit) => $habit->today_check_in?->status === CheckInStatus::Done
        )->count();

        $totalActive = $habits->count();
        $weekDoneCount = HabitCheckIn::query()
            ->where('user_id', $user->id)
            ->where('status', CheckInStatus::Done)
            ->whereDate('check_in_date', '>=', $weekStart)
            ->count();

        return [
            'timezone' => $timezone,
            'today' => $today,
            'habits' => $habits,
            'completed_today' => $completedToday,
            'total_active' => $totalActive,
            'completion_percent' => $totalActive > 0
                ? (int) round(($completedToday / $totalActive) * 100)
                : 0,
            'week_done_count' => $weekDoneCount,
            'longest_streak' => (int) $habits->max('current_streak'),
        ];
    }

    /**
     * @param  Collection<int, string>  $doneDates
     */
    public function streakFromDates(Collection $doneDates, string $timezone): int
    {
        if ($doneDates->isEmpty()) {
            return 0;
        }

        $cursor = Carbon::now($timezone)->startOfDay();
        if (! $doneDates->contains($cursor->toDateString())) {
            $cursor->subDay();
        }

        $streak = 0;
        while ($doneDates->contains($cursor->toDateString())) {
            $streak++;
            $cursor->subDay();
        }

        return $streak;
    }
}
