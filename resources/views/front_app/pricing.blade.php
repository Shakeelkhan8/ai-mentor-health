@extends('front_app.layouts.template')
@section('content')
<div class="banner-sec about-banner pricing-banner w-100 float-left bg-light-black">
    <div class="wrapper2">
        <div class="plans-section w-100 float-left bg-light-black">
            <div class="wrapper2">
                <div class="generic-title text-center">
                    <span data-aos="fade-up" data-aos-duration="600">SIMPLE PRICING</span>
                    <h2 data-aos="fade-up" data-aos-duration="600">Free experience + Pro subscription</h2>
                    <p data-aos="fade-up" data-aos-duration="600" class="mt-3" style="max-width: 40rem; margin-left: auto; margin-right: auto;">
                        {{ config('mentor.disclaimer') }}
                    </p>
                </div>
                <div class="row justify-content-center">
                    <div class="col-lg-5" data-aos="fade-up" data-aos-duration="600">
                        <div class="plan-box text-center bg-dark-black">
                            <h3>Free</h3>
                            <p class="plan-txt">Full MVP experience today</p>
                            <div class="price position-relative">
                                <span>$</span>0<span>/mo</span>
                            </div>
                            <div class="w-100 float-left">
                                <ul class="text-left list-unstyled">
                                    <li><i class="fas fa-check"></i> Health onboarding &amp; goals</li>
                                    <li><i class="fas fa-check"></i> Personalized AI coach</li>
                                    <li><i class="fas fa-check"></i> Habit check-ins</li>
                                    <li><i class="fas fa-check"></i> Weekly wellness plans</li>
                                </ul>
                            </div>
                            <div class="generic-btn">
                                <a href="{{ route('register') }}">Get Started</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5" data-aos="fade-up" data-aos-duration="600">
                        <div class="plan-box premium-plan text-center bg-dark-black">
                            <h3>Pro</h3>
                            <p class="plan-txt">Subscription concept (Stripe test mode)</p>
                            <div class="price position-relative">
                                <span>$</span>19<span>/mo</span>
                            </div>
                            <div class="w-100 float-left">
                                <ul class="text-left list-unstyled">
                                    <li><i class="fas fa-check"></i> Everything in Free</li>
                                    <li><i class="fas fa-check"></i> Stripe Checkout (test cards)</li>
                                    <li><i class="fas fa-check"></i> Webhook-synced subscription status</li>
                                    <li><i class="fas fa-check"></i> Ready for future entitlements</li>
                                </ul>
                            </div>
                            <div class="generic-btn">
                                <a href="{{ route('login') }}">Try Pro checkout</a>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="text-center mt-4" data-aos="fade-up" data-aos-duration="600">
                    Pro does <strong>not</strong> lock features in this MVP — soft CTA only so demos stay frictionless.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
