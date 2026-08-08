<?php

namespace App\Http\Controllers\Web\Habits;

use App\Domains\Habits\Actions\RecordHabitCheckIn;
use App\Domains\Habits\Enums\CheckInStatus;
use App\Domains\Habits\Models\Habit;
use App\Domains\Shared\Exceptions\DomainException;
use App\Http\Controllers\Web\WebController;
use App\Http\Requests\Habits\RecordHabitCheckInRequest;
use Carbon\Carbon;
use App\Domains\Habits\Models\Habit;
use App\Domains\Habits\Services\HabitDashboardService;
use App\Domains\Shared\Exceptions\DomainException;
use App\Http\Controllers\Web\WebController;
use App\Http\Requests\Habits\RecordHabitCheckInRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HabitDashboardController extends WebController
{
    public function index(HabitDashboardService $dashboardService): View
    {
        $user = auth()->user();
        $dashboard = $dashboardService->forUser($user);

        return view('backend_app.habits.dashboard', [
            'productName' => config('mentor.name'),
            'profile' => $user->profile,
            'disclaimer' => config('mentor.disclaimer'),
            ...$dashboard,
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

        $label = match ($request->validated('status')) {
            'done' => 'marked as done',
            'skipped' => 'skipped for today',
            'partial' => 'marked as partial',
            default => 'updated',
        };

        return back()->with('success', "\"{$habit->name}\" {$label}.");
    }
}
