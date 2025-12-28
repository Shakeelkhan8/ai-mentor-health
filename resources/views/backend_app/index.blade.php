@extends('backend_app.layouts.template')
@section('content')

<div class="layout-page">
    <!-- Navbar -->
    @include('backend_app.layouts.nav')
    <!-- / Navbar -->

    <!-- Content wrapper -->
    <div class="content-wrapper">
      <!-- Content -->

      <div class="container-xxl flex-grow-1 container-p-y">
        
        <!-- Chatbot Landing Section Start -->
        <section class="text-center my-5 py-5">
          <div class="row justify-content-center align-items-center">
            <div class="col-md-8">
              <h1 class="display-4 fw-bold mb-3">Meet Your AI Mentor</h1>
              <p class="lead mb-4">
                Welcome to <strong>AI Mentor</strong>, your intelligent virtual assistant trained like a seasoned cyclist. Whether you’re new to cycling or looking to fine-tune your skills, AI Mentor is here to guide you every step of the way.
              </p>
            
              <img src="{{ asset('assets/img/robo.png') }}" alt="AI Mentor Chatbot" class="img-fluid mb-4" style="max-width: 250px;">
              <br>
              <a href="{{ route('chatbot') }}" class="btn btn-primary btn-lg">
                Talk to Our Chatbot
              </a>
            </div>
          </div>
        </section>
        <!-- Chatbot Landing Section End -->

        <!-- Other Content Sections Could Go Here -->

      </div>
      <!-- / Content -->

      <!-- Footer -->
      @include('backend_app.layouts.footer')
      <!-- / Footer -->

      <div class="content-backdrop fade"></div>
    </div>
    <!-- Content wrapper -->
</div>

@endsection
