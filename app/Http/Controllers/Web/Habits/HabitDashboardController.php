<?php

namespace App\Http\Controllers\Web\Habits;

use App\Domains\Habits\Actions\RecordHabitCheckIn;
use App\Domains\Habits\Enums\CheckInStatus;
use App\Domains\Habits\Models\Habit;
use App\Domains\Shared\Exceptions\DomainException;
use App\Http\Controllers\Web\WebController;
use App\Http\Requests\Habits\RecordHabitCheckInRequest;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HabitDashboardController extends WebController
{
    public function index(): View
    {
        $user = auth()->user();
        $timezone = $user->profile?->timezone ?: config('app.timezone', 'UTC');
        $today = Carbon::now($timezone)->toDateString();

        $habits = Habit::query()
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->with(['checkIns' => fn ($q) => $q->whereDate('check_in_date', $today)])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(function (Habit $habit) {
                $habit->setAttribute('today_check_in', $habit->checkIns->first());

                return $habit;
            });

        return view('backend_app.index', [
            'productName' => config('mentor.name'),
            'profile' => $user->profile,
            'habits' => $habits,
            'today' => $today,
            'timezone' => $timezone,
            'hasActiveSubscription' => (bool) $user->activeSubscription(),
            'disclaimer' => config('mentor.disclaimer'),
        ]);
    }

    public function store(
        Habit $habit,
        RecordHabitCheckInRequest $request,
        RecordHabitCheckIn $recordHabitCheckIn,
    ): RedirectResponse {
        abort_unless((int) $habit->user_id === (int) $request->user()->id, 404);

        try {
            $recordHabitCheckIn->execute(
                $request->user(),
                $habit,
                $request->validated()
            );
        } catch (DomainException $e) {
            return back()->withErrors(['check_in' => $e->getMessage()]);
        }

        return back()->with('success', "Updated \"{$habit->name}\" for today.");
    }
}