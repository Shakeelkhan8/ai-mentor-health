@extends('backend_app.layouts.template')

@section('content')
<div class="container mt-5 text-center">
    <h2 class="text-success">Payment Successful!</h2>
    <p>Thank you for your payment. Your transaction has been completed successfully.</p>

    <a href="{{ route('dashboard') }}" class="btn btn-primary mt-3">Go to Dashboard</a>
</div>
@endsection
