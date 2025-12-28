@extends('backend_app.layouts.auth_template')
@section('content')
<div class="authentication-wrapper authentication-cover authentication-bg">
    <div class="authentication-inner row">
      <!-- /Left Text -->
      <div class="d-none d-lg-flex col-lg-7 p-0">
        <div class="auth-cover-bg d-flex justify-content-center align-items-center">
          <img
            src="{{ asset('assets/img/logo/hillview-png-1.png') }}"
            alt="auth-login-cover"
            class="w-50"
          />

          <img
            src="{{ asset('assets/img/illustrations/bg-shape-image-light.png') }}"
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
          <!-- Logo -->
          <div class="app-brand mb-4">
            <a href="index.html" class="app-brand-link gap-2">
              <span class="app-brand-logo demo"></span>
            </a>
          </div>
          <!-- /Logo -->
          <h3 class="mb-1">Welcome to Hill View! 👋</h3>
          <p class="mb-4">Please sign-in to your account and start the adventure</p>

          <form method="POST" action="{{ route('login') }}" id="formAuthentication" class="mb-3">
            @csrf
            <div class="mb-3">
              <label for="email" class="form-label">Email or Username</label>
              <input
                type="text"
                class="form-control"
                id="email"
                name="email"
                placeholder="Enter your email or username"
                autofocus />
              @error('email')
                <span class="text-danger">{{ $message }}</span>
              @enderror
            </div>
            <div class="mb-3 form-password-toggle">
              <div class="d-flex justify-content-between">
                <label class="form-label" for="password">Password</label>
                <a href="{{ route('password.reset') }}">
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
                <span class="text-danger">{{ $message }}</span>
              @enderror
            </div>
            <div class="mb-3">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember-me" name="remember" />
                <label class="form-check-label" for="remember-me"> Remember Me </label>
              </div>
            </div>
          

            <!-- Add the reCAPTCHA widget -->
            <div class="mb-3">
              <div class="g-recaptcha" data-sitekey="6Ld8-rkqAAAAACNIA0MRMndROjvUDFLOS-xcVjUk"></div>
              @error('g-recaptcha-response')
                <span class="text-danger">{{ $message }}</span>
              @enderror
            </div>

            <button class="btn btn-primary d-grid w-100">Sign in</button>
          </form>

          {{-- <p class="text-center">
            <span>New on our platform?</span>
            <a href="auth-register-cover.html">
              <span>Create an account</span>
            </a>
          </p> --}}
        </div>
      </div>
      <!-- /Login -->
    </div>
</div>

<!-- Load the reCAPTCHA API script -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endsection
