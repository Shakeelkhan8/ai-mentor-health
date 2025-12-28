@extends('front_app.layouts.template')
@section('content')
<section class="contact-banner banner-sec about-banner w-100 float-left bg-light-black">
    <!-- ... existing banner content ... -->
</section>
<!-- banner section -->
<!-- contact section-->
<section class="contact-section w-100 float-left padding-top padding-bottom bg-light-black" id="form-contact">
    <div class="wrapper2">
        <div class="generic-title text-center">
            <span data-aos="fade-up" data-aos-duration="600">CONTACT US</span>
            <h2 data-aos="fade-up" data-aos-duration="600">
                Feel free to use the contact form <br>
                to the right to reach out to us, or write us <br>
                the old fashion way.
            </h2>
        </div>
        <div class="contct-form-section">
            <div class="row align-items-center">
                <div class="col-lg-6 col-md-5 order-md-0 order-2 ">
                    <div class="contact-details">
                        <ul class="mb-0 list-unstyled">
                            <li class="position-relative" data-aos="fade-up" data-aos-duration="600">
                                <i class="fas fa-map-marker-alt"></i> 551 Swanston Street, Melbourne <br>
                                Victoria 3053 Australia
                            </li>
                            <li class="position-relative" data-aos="fade-up" data-aos-duration="600">
                                <i class="fas fa-phone-volume"></i>
                                <a href="tel:+61383766284">+61 3 8376 6284</a>
                            </li>
                            <li class="position-relative mb-0" data-aos="fade-up" data-aos-duration="600">
                                <i class="fas fa-envelope"></i>
                                <a href="mailto:info@designingmedia.com">info@designingmedia.com</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6 col-md-7">
                    <div class="contact-form-box">
                        <form class="bg-dark-black" action="{{ route('contact.store') }}" method="post">
                            @csrf
                            <h3>How Can We Help?</h3>
                            <ul class="list-unstyled mb-0">
                               <li class="position-relative">
                                   <i class="fas fa-user-alt"></i>
                                   <input class="w-100" placeholder="Name" type="text" name="name" id="name" value="{{ old('name') }}">
                                   @error('name')
                                       <small class="text-danger">{{ $message }}</small>
                                   @enderror
                               </li>
                               <li class="position-relative">
                                   <i class="fas fa-phone-volume"></i>
                                   <input class="w-100" type="text" placeholder="Phone Number" name="phone" id="phone" value="{{ old('phone') }}">
                                   @error('phone')
                                       <small class="text-danger">{{ $message }}</small>
                                   @enderror
                               </li>
                               <li class="position-relative">
                                   <i class="fas fa-envelope"></i>
                                   <input class="w-100" type="email" placeholder="Email Address" name="email" id="email" value="{{ old('email') }}">
                                   @error('email')
                                       <small class="text-danger">{{ $message }}</small>
                                   @enderror
                               </li>
                               <li class="w-100 position-relative">
                                   <i class="fas fa-pencil-alt"></i>
                                   <textarea class="w-100" placeholder="Message" name="message" id="subject">{{ old('subject') }}</textarea>
                                   @error('message')
                                       <small class="text-danger">{{ $message }}</small>
                                   @enderror
                               </li>
                               <li class="mb-0 w-100 d-inline-block generic-btn">
                                   <button type="submit" id="submit">Get Started</button>
                               </li>
                            </ul>
                         </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
