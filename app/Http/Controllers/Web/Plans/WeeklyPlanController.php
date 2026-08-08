<?php

namespace App\Http\Controllers\Web\Plans;

use App\Domains\Plans\Actions\GenerateWeeklyPlan;
use App\Domains\Plans\Models\WeeklyPlan;
use App\Domains\Plans\Services\WeeklyPlanGenerator;
use App\Domains\Shared\Exceptions\DomainException;
use App\Http\Controllers\Web\WebController;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WeeklyPlanController extends WebController
{
    public function show(): View
    {
        $user = auth()->user();
        $timezone = $user->profile?->timezone ?: config('app.timezone', 'UTC');
        $weekStart = Carbon::now($timezone)->startOfWeek(Carbon::MONDAY)->toDateString();

        $plan = WeeklyPlan::query()
            ->where('user_id', $user->id)
            ->whereDate('week_start', $weekStart)
            ->first();

        return view('backend_app.plans.weekly', [
            'productName' => config('mentor.name'),
            'profile' => $user->profile,
            'plan' => $plan,
            'weekStart' => $weekStart,
            'timezone' => $timezone,
            'planDisclaimer' => WeeklyPlanGenerator::PLAN_DISCLAIMER,
            'disclaimer' => config('mentor.disclaimer'),
            'hasActiveSubscription' => (bool) auth()->user()->activeSubscription(),
        ]);
    }

    public function generate(GenerateWeeklyPlan $generateWeeklyPlan): RedirectResponse
    {
        try {
            $plan = $generateWeeklyPlan->execute(auth()->user());
        } catch (DomainException $e) {
            return back()->withErrors(['plan' => $e->getMessage()]);
        }

        $source = $plan->getAttribute('generation_source');
        $message = $source === 'fallback'
            ? 'Weekly plan saved using a safe template (AI was unavailable or returned invalid data).'
            : 'Your personalized weekly plan is ready.';

        return redirect()
            ->route('plans.weekly.show')
            ->with('success', $message);
    }
}
