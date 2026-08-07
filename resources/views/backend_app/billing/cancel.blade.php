@extends('backend_app.layouts.template')
@section('content')
<div class="layout-page">
    @include('backend_app.layouts.nav')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-body">
                    <h3 class="mb-2">Checkout canceled</h3>
                    <p class="text-muted">No charge was made. You can start again whenever you’re ready.</p>
                    <a href="{{ route('billing.show') }}" class="btn btn-primary">Back to Billing</a>
                </div>
            </div>
            @include('backend_app.layouts.footer')
        </div>
    </div>
</div>
@endsection
