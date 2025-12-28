
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
        <form method="POST" action="{{ route('register') }}">
            @csrf

        <div class="w-px-400 mx-auto">
          <!-- Logo -->
          <div class="app-brand mb-4">
            <a href="index.html" class="app-brand-link gap-2">
              <span class="app-brand-logo demo">

              </span>
            </a>
          </div>
          <!-- /Logo -->
          <h3 class="mb-1">Welcome to Mind Mentor Health! 👋</h3>
          <p class="mb-4">Please sign-up to your account and start the adventure</p>

          <form id="formAuthentication" class="mb-3" action="index.html" method="POST">

            <div class="mb-3">
                <x-input-label for="name" :value="__('Name')" />
                <input id="name" class="block form-control mt-1 w-full" placeholder="Enter Name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input
                type="text"
                class="form-control"
                id="email"
                name="email"
                placeholder="Enter your email or username"
                autofocus />
                @error('email')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>

            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" />
    
                <input id="password" class="block form-control mt-1 w-full"
                                type="password"
                                name="password"
                                required autocomplete="new-password" placeholder="Enter password" />
    
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
    
            <!-- Confirm Password -->
            <div class="mt-4">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
    
                <input id="password_confirmation" class="block mt-1 w-ful form-control"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" placeholder="Confirm password" />
    
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

      
            <button class="btn btn-primary d-grid w-100 mt-4">Sign Up</button>
          </form>

          {{-- <p class="text-center">
            <span>New on our platform?</span>
            <a href="auth-register-cover.html">
              <span>Create an account</span>
            </a>
          </p> --}}



        </div>
      </div>
    </form>
      <!-- /Login -->
    </div>
  </div>

@endsection
