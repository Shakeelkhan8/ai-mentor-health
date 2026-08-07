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
                    <h3 class="mb-1">{{ $productName }} Dashboard</h3>
                    <p class="text-muted mb-0">
                        Today ({{ $today }}) · {{ $timezone }}
                        @if($profile?->primary_goal)
                            · Goal: {{ $profile->primary_goal }}
                        @endif
                    </p>
                </div>
                <a href="{{ route('coach.index') }}" class="btn btn-primary">Talk to AI Coach</a>
            </div>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <div class="row g-3 mb-4">
                <div class="col-md-3 col-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="text-muted small">Today’s completion</div>
                            <div class="fs-3 fw-semibold">{{ $completion_percent }}%</div>
                            <div class="small">{{ $completed_today }}/{{ $total_active }} habits done</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="text-muted small">Active habits</div>
                            <div class="fs-3 fw-semibold">{{ $total_active }}</div>
                            <div class="small">From your onboarding focus</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="text-muted small">This week</div>
                            <div class="fs-3 fw-semibold">{{ $week_done_count }}</div>
                            <div class="small">Completed check-ins</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="text-muted small">Best streak</div>
                            <div class="fs-3 fw-semibold">{{ $longest_streak }}</div>
                            <div class="small">Consecutive done days</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Today’s habits</h5>
                </div>
                <div class="card-body">
                    @forelse ($habits as $habit)
                        @php
                            $todayStatus = $habit->today_check_in?->status;
                        @endphp
                        <div class="border rounded-3 p-3 mb-3">
                            <div class="d-flex flex-wrap justify-content-between gap-3">
                                <div>
                                    <div class="fw-semibold">{{ $habit->name }}</div>
                                    <div class="text-muted small mb-1">
                                        {{ $habit->focus_area->label() }}
                                        · streak {{ $habit->current_streak }} day{{ $habit->current_streak === 1 ? '' : 's' }}
                                    </div>
                                    @if($habit->description)
                                        <div class="small">{{ $habit->description }}</div>
                                    @endif
                                    <div class="mt-2">
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
                                </div>
                                <div class="d-flex flex-wrap gap-2 align-items-start">
                                    <form method="POST" action="{{ route('habits.check-ins.store', $habit) }}">
                                        @csrf
                                        <input type="hidden" name="status" value="done">
                                        <button class="btn btn-sm btn-success" type="submit">Done</button>
                                    </form>
                                    <form method="POST" action="{{ route('habits.check-ins.store', $habit) }}">
                                        @csrf
                                        <input type="hidden" name="status" value="partial">
                                        <button class="btn btn-sm btn-outline-warning" type="submit">Partial</button>
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

            <div class="alert alert-warning mb-0">
                {{ $disclaimer }}
            </div>

            @include('backend_app.layouts.footer')
        </div>
    </div>
</div>
@endsection
