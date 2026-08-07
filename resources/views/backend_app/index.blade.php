@extends('backend_app.layouts.template')
@section('content')
@php
    use App\Domains\Habits\Enums\CheckInStatus;
@endphp
<div class="layout-page">
    @include('backend_app.layouts.nav')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                <div>
                    <h3 class="mb-1">{{ $productName }}</h3>
                    <p class="text-muted mb-0">
                        Your wellness companion · Today {{ $today }} ({{ $timezone }})
                        @if($profile?->primary_goal)
                            · Goal: {{ $profile->primary_goal }}
                        @endif
                    </p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    @if(config('mentor.features.ai_coach'))
                        <a href="{{ route('coach.index') }}" class="btn btn-primary">Talk to AI Coach</a>
                    @endif
                    @if(config('mentor.features.weekly_plans'))
                        <a href="{{ route('plans.weekly.show') }}" class="btn btn-outline-primary">Weekly Plan</a>
                    @endif
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <div class="alert alert-warning">
                {{ $disclaimer }}
            </div>

            @if (! $hasActiveSubscription && config('mentor.features.subscriptions'))
                <div class="alert alert-primary d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <strong>Free experience</strong> includes coaching, check-ins, and weekly plans.
                        <span class="text-muted">Pro is available via Stripe test checkout — features are not locked yet.</span>
                    </div>
                    <a href="{{ route('billing.show') }}" class="btn btn-sm btn-primary">View Pro</a>
                </div>
            @endif

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Today’s habit check-ins</h5>
                </div>
                <div class="card-body">
                    @forelse ($habits as $habit)
                        @php $todayStatus = $habit->today_check_in?->status; @endphp
                        <div class="border rounded-3 p-3 mb-3">
                            <div class="d-flex flex-wrap justify-content-between gap-3">
                                <div>
                                    <div class="fw-semibold">{{ $habit->name }}</div>
                                    <div class="text-muted small mb-2">{{ $habit->focus_area->label() }}</div>
                                    @if($todayStatus === CheckInStatus::Done)
                                        <span class="badge bg-success">Done today</span>
                                    @elseif($todayStatus === CheckInStatus::Partial)
                                        <span class="badge bg-warning">Partial today</span>
                                    @elseif($todayStatus === CheckInStatus::Skipped)
                                        <span class="badge bg-secondary">Skipped today</span>
                                    @else
                                        <span class="badge bg-label-primary">Not checked in</span>
                                    @endif
                                </div>
                                <div class="d-flex flex-wrap gap-2 align-items-start">
                                    <form method="POST" action="{{ route('habits.check-ins.store', $habit) }}">
                                        @csrf
                                        <input type="hidden" name="status" value="done">
                                        <button class="btn btn-sm btn-success" type="submit">Done</button>
                                    </form>
                                    <form method="POST" action="{{ route('habits.check-ins.store', $habit) }}">
                                        @csrf
                                        <input type="hidden" name="status" value="skipped">
                                        <button class="btn btn-sm btn-outline-secondary" type="submit">Skip</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="mb-0 text-muted">No active habits yet. Complete onboarding to generate starter habits.</p>
                    @endforelse
                </div>
            </div>

            @include('backend_app.layouts.footer')
        </div>
    </div>
</div>
@endsection
