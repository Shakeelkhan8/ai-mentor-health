@extends('backend_app.layouts.auth_template')
@section('content')
<div class="authentication-wrapper authentication-cover authentication-bg">
    <div class="authentication-inner row">
      <!-- /Left Text -->
      <div class="d-none d-lg-flex col-lg-7 p-0" >
        <div class="auth-cover-bg  d-flex justify-content-center align-items-center">
          <img
            src="{{asset('assets/images/ai-images/andrea-de-santis-zwd435-ewb4-unsplash.jpg')}}"
            alt="auth-login-cover"
            class="w-50 rounded-3 "
            />

          <img
            src="../../assets/img/illustrations/bg-shape-image-light.png"
            alt="auth-login-cover"
            class="platform-bg"
            data-app-light-img="illustrations/bg-shape-image-light.png"
            data-app-dark-img="illustrations/bg-shape-image-dark.png" />
        </div>
      </div>
      <!-- /Left Text -->

      <!-- Login -->

      <div class="d-flex col-12 col-lg-5 align-items-center p-sm-5 p-4">
        <div class="w-px-400 mx-auto">
          <h3 class="mb-1">Welcome to {{ config('mentor.name') }}! 👋</h3>
          <p class="mb-4">Please sign in to your account and start the adventure</p>

          @if (config('mentor.demo.enabled'))
            <form method="POST" action="{{ route('demo.login') }}" class="mb-4">
              @csrf
              <button type="submit" class="btn btn-label-primary d-grid w-100">Try the demo (no signup)</button>
              <small class="text-muted d-block mt-1">Opens a shared account with sample habits, plans and coach chat.</small>
            </form>
          @endif

          <form method="POST" action="{{ route('login') }}" class="mb-3">
            @csrf
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input
                type="email"
                class="form-control"
                id="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="Enter your email"
                autofocus />
                @error('email')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="mb-3 form-password-toggle">
              <div class="d-flex justify-content-between">
                <label class="form-label" for="password">Password</label>
                <a href="{{route('password.request')}}">
                  <small>Forgot Password?</small>
                </a>
              </div>
              <div class="input-group input-group-merge">
                <input
                  type="password"
                  id="password"
                  class="form-control"
                  name="password"
                  placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                  aria-describedby="password" />
                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
              </div>
              @error('password')
              <span class="text-danger">{{$message}}</span>
              @enderror
            </div>
            <div class="mb-3">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember-me" name="remember" />
                <label class="form-check-label" for="remember-me"> Remember Me </label>
              </div>
            </div>
            <div class="mb-3">
              <a href="{{ route('register') }}">
                <small>Don't have an account? Sign up</small>
              </a>
            </div>
            <button type="submit" class="btn btn-primary d-grid w-100">Sign in</button>
          </form>
        </div>
      </div>
      <!-- /Login -->
    </div>
  </div>

@endsection
