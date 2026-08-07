@extends('backend_app.layouts.template')
@section('content')
<div class="layout-page">
    @include('backend_app.layouts.nav')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                <div>
                    <h3 class="mb-1">{{ $productName }} Subscription</h3>
                    <p class="text-muted mb-0">Unlock ongoing personalized coaching with a Pro subscription (Stripe test mode).</p>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <div class="card mb-4">
                <div class="card-body">
                    @if ($subscription)
                        <h5 class="mb-2">Status: {{ $subscription->status->value }}</h5>
                        <p class="mb-1 text-muted">Plan is active for your account.</p>
                        @if ($subscription->current_period_ends_at)
                            <p class="mb-0 small">Current period ends {{ $subscription->current_period_ends_at->toDayDateTimeString() }}</p>
                        @endif
                    @else
                        <h5 class="mb-2">Free experience</h5>
                        <p class="mb-2 text-muted">
                            Coaching, habit check-ins, and weekly plans are available now without a paywall.
                        </p>
                        <h6 class="mb-2">Pro subscription (concept)</h6>
                        <p class="mb-3 text-muted">
                            Subscribe via Stripe test Checkout to demonstrate billing. Features are <strong>not</strong> blocked behind Pro yet.
                        </p>
                        @if ($priceConfigured)
                            <form method="POST" action="{{ route('billing.checkout') }}">
                                @csrf
                                <button type="submit" class="btn btn-primary">Subscribe with Stripe (test mode)</button>
                            </form>
                        @else
                            <div class="alert alert-warning mb-0">
                                Stripe price is not configured. Set <code>STRIPE_PRICE_ID</code> (and test keys) in your environment.
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            @if ($latestSubscription && ! $subscription)
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="text-muted small">Latest subscription status</div>
                        <div class="fw-semibold">{{ $latestSubscription->status->value }}</div>
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
