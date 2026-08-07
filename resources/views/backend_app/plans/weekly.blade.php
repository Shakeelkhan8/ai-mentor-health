@extends('backend_app.layouts.template')
@section('content')
<div class="layout-page">
    @include('backend_app.layouts.nav')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                <div>
                    <h3 class="mb-1">Weekly Plan</h3>
                    <p class="text-muted mb-0">
                        Week of {{ $weekStart }} · {{ $timezone }}
                        @if($profile?->primary_goal)
                            · Goal: {{ $profile->primary_goal }}
                        @endif
                    </p>
                </div>
                <form method="POST" action="{{ route('plans.weekly.generate') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        {{ $plan ? 'Regenerate plan' : 'Generate plan' }}
                    </button>
                </form>
            </div>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <div class="alert alert-warning">
                {{ $planDisclaimer }}
            </div>

            @if (! ($hasActiveSubscription ?? false) && config('mentor.features.subscriptions'))
                <div class="alert alert-primary d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <span>Enjoying personalized plans? Pro checkout is available in test mode — nothing is locked yet.</span>
                    <a href="{{ route('billing.show') }}" class="btn btn-sm btn-primary">View Pro</a>
                </div>
            @endif

            @if ($plan)
                <div class="card mb-4">
                    <div class="card-body">
                        <h4 class="mb-2">{{ $plan->title }}</h4>
                        <p class="mb-3">{{ $plan->summary }}</p>
                        <div class="text-muted small mb-3">
                            Status: {{ $plan->status->value }}
                            @if($plan->generated_at)
                                · Generated {{ $plan->generated_at->diffForHumans() }}
                            @endif
                        </div>

                        <div class="list-group">
                            @foreach (($plan->items ?? []) as $item)
                                <div class="list-group-item">
                                    <div class="d-flex flex-wrap justify-content-between gap-2">
                                        <div>
                                            <div class="fw-semibold">{{ $item['action'] ?? '' }}</div>
                                            <div class="small text-muted">{{ $item['target'] ?? '' }}</div>
                                        </div>
                                        <span class="badge bg-label-primary align-self-start text-uppercase">
                                            {{ $item['category'] ?? 'habit' }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <div class="card mb-4">
                    <div class="card-body">
                        <p class="mb-0 text-muted">
                            No plan for this week yet. Generate a personalized plan based on your goals, focus areas, and habits.
                        </p>
                    </div>
                </div>
            @endif

            <div class="alert alert-secondary mb-0">
                {{ $disclaimer }}
            </div>

            @include('backend_app.layouts.footer')
        </div>
    </div>
</div>
@endsection
