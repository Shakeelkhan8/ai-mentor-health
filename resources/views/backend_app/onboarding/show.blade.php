@extends('backend_app.layouts.template')
@section('content')
<div class="layout-page">
    @include('backend_app.layouts.nav')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body p-4 p-md-5">
                            <h3 class="mb-1">Welcome to {{ $productName }}</h3>
                            <p class="text-muted mb-4">
                                Tell us what you want to improve. We’ll personalize your coaching focus and create starter habits.
                            </p>

                            <div class="alert alert-warning" role="alert">
                                {{ $disclaimer }}
                            </div>

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('onboarding.store') }}">
                                @csrf

                                <div class="mb-4">
                                    <label class="form-label fw-semibold">What do you want to focus on?</label>
                                    <div class="row g-2">
                                        @foreach ($focusAreas as $area)
                                            <div class="col-md-6">
                                                <div class="form-check border rounded-3 p-3 h-100">
                                                    <input
                                                        class="form-check-input ms-0 me-2"
                                                        type="checkbox"
                                                        name="focus_areas[]"
                                                        id="focus_{{ $area->value }}"
                                                        value="{{ $area->value }}"
                                                        @checked(collect(old('focus_areas', $profile?->focus_areas ?? []))->contains($area->value))
                                                    >
                                                    <label class="form-check-label" for="focus_{{ $area->value }}">
                                                        {{ $area->label() }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="primary_goal" class="form-label fw-semibold">Your primary goal</label>
                                    <textarea
                                        class="form-control"
                                        id="primary_goal"
                                        name="primary_goal"
                                        rows="3"
                                        maxlength="500"
                                        placeholder="Example: Build a consistent sleep routine and reduce late-night scrolling."
                                        required
                                    >{{ old('primary_goal', $profile?->primary_goal) }}</textarea>
                                </div>

                                <div class="mb-4">
                                    <label for="timezone" class="form-label fw-semibold">Timezone</label>
                                    <select class="form-select" id="timezone" name="timezone">
                                        @foreach ($timezones as $timezone)
                                            <option value="{{ $timezone }}" @selected($timezone === old('timezone', $selectedTimezone))>
                                                {{ $timezone }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">Used for daily check-ins and reminders later.</div>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    Start coaching
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @include('backend_app.layouts.footer')
        </div>
    </div>
</div>
@endsection
