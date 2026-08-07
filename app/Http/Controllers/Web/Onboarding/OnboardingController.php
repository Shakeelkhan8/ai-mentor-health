<?php

namespace App\Http\Controllers\Web\Onboarding;

use App\Domains\Identity\Actions\CompleteOnboarding;
use App\Domains\Identity\Enums\HealthFocusArea;
use App\Domains\Shared\Exceptions\DomainException;
use App\Http\Controllers\Web\WebController;
use App\Http\Requests\Identity\CompleteOnboardingRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OnboardingController extends WebController
{
    public function show(): View|RedirectResponse
    {
        $profile = auth()->user()->profile;

        if ($profile?->hasCompletedOnboarding()) {
            return redirect()->route('dashboard');
        }

        return view('backend_app.onboarding.show', [
            'productName' => config('mentor.name'),
            'focusAreas' => HealthFocusArea::cases(),
            'profile' => $profile,
            'disclaimer' => config('mentor.disclaimer'),
            'timezones' => timezone_identifiers_list(),
            'selectedTimezone' => $profile?->timezone ?: config('app.timezone', 'UTC'),
        ]);
    }

    public function store(
        CompleteOnboardingRequest $request,
        CompleteOnboarding $completeOnboarding,
    ): RedirectResponse {
        try {
            $completeOnboarding->execute(
                $request->user(),
                $request->validated()
            );
        } catch (DomainException $e) {
            return back()
                ->withInput()
                ->withErrors(['onboarding' => $e->getMessage()]);
        }

        return redirect()
            ->route('dashboard')
            ->with('success', 'Welcome to '.config('mentor.name').'! Your coaching profile is ready.');
    }
}
