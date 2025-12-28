@extends('front_app.layouts.template')
@section('content')
<section class="index2-banner-sec w-100 float-left bg-light-black">
    <div class="wrapper2">
        <div class="banner-left-sec">
            <h1 data-aos="fade-up" data-aos-duration="600">The <span>Future</span> in <br> AI Mentor Health</h1>
            <p data-aos="fade-up" data-aos-duration="600">Empowering Healthcare with AI Mentorship</p>
            <div class="generic-btn" data-aos="fade-up" data-aos-duration="600">
                <a href="{{route('login')}}">Get Started</a>
            </div>
        </div>

    </div>

</section>
<!-- banner section -->
<!-- services section -->
{{-- <div class="service-slider-sec w-100 float-left padding-top padding-bottom bg-dark-black">
    <div class="service-slider-box">
        <div id="owlsliderone" class="owl-carousel owl-theme index2-slider" data-aos="fade-up" data-aos-duration="600">
            <div class="item">
                <figure class="mb-0">
                    <img src="https://plus.unsplash.com/premium_photo-1682824038225-834e4244f1e6?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTd8fGFpJTIwYnJhaW58ZW58MHx8MHx8fDA%3D" alt="slider-img1">
                </figure>
            </div>
            <div class="item">
                <figure class="mb-0">
                    <img src="https://html.designingmedia.com/aimentor/assets/images/slider-img2.png" alt="slider-img2">
                </figure>
            </div>
            <div class="item">
                <figure class="mb-0">
                    <img src="https://html.designingmedia.com/aimentor/assets/images/slider-img3.png" alt="slider-img3">
                </figure>
            </div>
            <div class="item">
                <figure class="mb-0">
                    <img src="https://html.designingmedia.com/aimentor/assets/images/slider-img4.png" alt="slider-img4">
                </figure>
            </div>
            <div class="item">
                <figure class="mb-0">
                    <img src="https://html.designingmedia.com/aimentor/assets/images/slider-img5.png" alt="slider-img5">
                </figure>
            </div>
            <div class="item">
                <figure class="mb-0">
                    <img src="https://html.designingmedia.com/aimentor/assets/images/slider-img1.png" alt="slider-img1">
                </figure>
            </div>
            <div class="item">
                <figure class="mb-0">
                    <img src="https://html.designingmedia.com/aimentor/assets/images/slider-img1.png" alt="slider-img1">
                </figure>
            </div>
            <div class="item">
                <figure class="mb-0">
                    <img src="https://html.designingmedia.com/aimentor/assets/images/slider-img2.png" alt="slider-img2">
                </figure>
            </div>
            <div class="item">
                <figure class="mb-0">
                    <img src="https://html.designingmedia.com/aimentor/assets/images/slider-img3.png" alt="slider-img3">
                </figure>
            </div>
            <div class="item">
                <figure class="mb-0">
                    <img src="https://html.designingmedia.com/aimentor/assets/images/slider-img4.png" alt="slider-img4">
                </figure>
            </div>
        </div>
    </div>
</div> --}}
<!-- services section -->
<!-- solution section -->
<section class="w-100 float-left Solution-con bg-dark-black glow-img position-relative">
    <div class="wrapper2">
        <div class="generic-title text-center">
            <span class="text-uppercase" data-aos="fade-up" data-aos-duration="600">Solution</span>
            <h2 data-aos="fade-up" data-aos-duration="600">AiMentor's AI-driven support is here to alleviate stress <br>
                and provide  assistance in overcoming . <br>
                suicidal thoughts.</h2>
        </div>
        <div class="Solution-box">
            <div class="Solution-box-item text-center" data-aos="fade-up" data-aos-duration="600">
                <figure class="mb-0">
                    <img src="{{asset('assets/images/ai-images/image_1.jpg')}}" alt="sloution-img">
                </figure>
                <div class="Solution-box-content">
                    <a href="service.html">
                        <h3 class="mb-0">Accessible Mental Health Support
                       .</h3>
                    </a>
                </div>
            </div>
            <div class="Solution-box-item text-center" data-aos="fade-up" data-aos-duration="600">
                <figure class="mb-0">
                    <img src="{{asset('assets/images/ai-images/fetchpik.com-rMPIfeT2nG.jpg')}}" alt="sloution-img">
                </figure>
                <div class="Solution-box-content">
                    <a href="service.html">
                        <h3 class="mb-0">Bridge to Professional Help</h3>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- solution section -->
<!-- DESIGN SECTION -->

<section class="w-100 float-left design-con position-relative">
    <div class="wrapper2">
        <div class="design-box">
            <div class="design-box-content">
                <span class="d-block" data-aos="fade-up" data-aos-duration="600">MENTAL HEALTH SUPPORT</span>
                <h2 data-aos="fade-up" data-aos-duration="600">AI-Powered Mental Health Chatbot</h2>
                <p data-aos="fade-up" data-aos-duration="600">Our AI-driven tools streamline mental health support, fostering a supportive environment for users to address their concerns and seek help.</p>
                <div class="generic-list">
                    <ul class="list-unstyled">
                        <li data-aos="fade-up" data-aos-duration="600">Immediate responses and guidance</li>
                        <li data-aos="fade-up" data-aos-duration="600">Tailored information and coping strategies</li>
                        <li data-aos="fade-up" data-aos-duration="600">Confidential and judgment-free interaction</li>
                        <li data-aos="fade-up" data-aos-duration="600">Bridge to professional mental health services</li>
                    </ul>
                </div>
                <div class="generic-btn" data-aos="fade-up" data-aos-duration="600">
                    <a href="about.html">Get Started</a>
                </div>
            </div>
            <div class="design-box-item">
                <div class="design-service">
                    <div class="collection-box" data-aos="fade-up" data-aos-duration="600">
                        <figure>
                            <img src="assets/images/collection-img1.png" alt="collection-img1">
                        </figure>
                        <h4>Immediate Support</h4>
                        <p>Get immediate responses and guidance to address your concerns.</p>
                        <a href="service.html"><i class="fas fa-arrow-right"></i></a>
                    </div>
                    <div class="collection-box" data-aos="fade-up" data-aos-duration="600">
                        <figure>
                            <img src="assets/images/collection-img2.png" alt="collection-img1">
                        </figure>
                        <h4>Tailored Information</h4>
                        <p>Receive tailored information and coping strategies suited to your needs.</p>
                        <a href="service.html"><i class="fas fa-arrow-right"></i></a>
                    </div>
                    <div class="collection-box" data-aos="fade-up" data-aos-duration="600">
                        <figure>
                            <img src="assets/images/collection-img3.png" alt="collection-img1">
                        </figure>
                        <h4>Confidential Interaction</h4>
                        <p>Engage in confidential and judgment-free interactions to discuss your concerns.</p>
                        <a href="service.html"><i class="fas fa-arrow-right"></i></a>
                    </div>
                    <div class="collection-box" data-aos="fade-up" data-aos-duration="600">
                        <figure>
                            <img src="assets/images/collection-img4.png" alt="collection-img1">
                        </figure>
                        <h4>Bridge to Professional Help</h4>
                        <p>Connect seamlessly to professional mental health services when needed.</p>
                        <a href="service.html"><i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- DESIGN SECTION -->
<!-- PRICING PLAN -->
<section class="w-100 float-left home-price-con bg-dark-black">
    <div class="wrapper2">
        <div id="light">
            <a class="boxclose" id="boxclose" onclick="lightbox_close();"></a>
            <iframe src="https://www.youtube.com/embed/kNXMJBQ6oL4" allowfullscreen=""></iframe>
        </div>
        <div class="generic-title text-center">
            <span class="text-uppercase" data-aos="fade-up" data-aos-duration="600">Affordable Plans</span>
            <h2 data-aos="fade-up" data-aos-duration="600"> Pricing Plans</h2>
        </div>
        <div class="home-price-box">
            <div class="home-price-item" data-aos="fade-up" data-aos-duration="600">
                <div id="fade1" onClick="lightbox_close();"></div>
                <div class="vedio-img">
                    <a href="javascript:void(0)" onclick="lightbox_open();" class="position-relative black-layer">
                        <img class="thumb poster-con index1-poster" style="cursor: pointer;"
                            src="https://html.designingmedia.com/aimentor/assets/images/vedio-img.jpg" alt="vedio-img">
                        <div class="video-wrap position-absolute">
                            <img src="assets/images/play-icon.png" alt="play-icon">
                        </div>
                    </a>
                </div>
            </div>
            <div class="home-price-inner-box">
                <div class="home-price-box-item plan-box" data-aos="fade-up" data-aos-duration="600">
                    <div class="home-price-lft-con" >
                        <h3>Business Plan</h3>
                        <span class="d-block">For Private individuals</span>
                        <div class="price-value">
                            <span class="d-block">Starting at:</span>
                            <div class="price position-relative">
                                <span>$</span>8<span>/mo</span>
                            </div>
                        </div>
                    </div>
                    <div class="home-price-rt-con">
                        <div class="price-list">
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check"></i> 10 Projects</li>
                                <li><i class="fas fa-check"></i> Download prototypes</li>
                                <li><i class="fas fa-check"></i> Graphic Images</li>
                            </ul>
                            <div class="generic-btn">
                                <a href="pricing.html">Get Started</a>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- <div class="home-price-box-item plan-box" data-aos="fade-up" data-aos-duration="600">
                    <div class="home-price-lft-con ">
                        <h3>Premium Plan</h3>
                        <span class="d-block">For Private companies</span>
                        <div class="price-value">
                            <span class="d-block">Starting at:</span>
                            <div class="price position-relative">
                                <span>$</span>19<span>/mo</span>
                            </div>
                        </div>
                    </div>
                    <div class="home-price-rt-con">
                        <div class="price-list">
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check"></i> 100 Projects</li>
                                <li><i class="fas fa-check"></i> Download prototypes</li>
                                <li><i class="fas fa-check"></i> Graphic Images</li>
                            </ul>
                            <div class="generic-btn">
                                <a href="pricing.html">Get Started</a>
                            </div>
                        </div>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>
</section>
<!-- PRICING PLAN -->
<!-- gallery section -->

<!-- gallery section-->
<!--  -->
<section class="w-100 float-left testimonial-slider">
    <div class="wrapper2">
        <div class="generic-title text-center">
            <span class="text-uppercase" data-aos="fade-up" data-aos-duration="600">What Say our customers</span>
            <h2 data-aos="fade-up" data-aos-duration="600">Client Testimonials</h2>
        </div>
        <div id="carouselExampleControls" class="carousel slide" data-ride="carousel" data-aos="fade-up" data-aos-duration="600">
            <div class="carousel-inner">
              <div class="carousel-item active">
                <div class="testimonial-content text-center">
                    <figure>
                        <img src="assets/images/quote-icon.png" alt="quote-icon">
                    </figure>
                    <p>Nemo enim ipsam voluptatem quia voluptas sit as <br>
                        pernatur iaut odite aut fugit, sed quia consequunt ur magni dolores eos qui rati <br> one voluptatem sequi dolor porro quisquam nesciunt.</p>
                    <div class="auther-con">
                        <figure>
                            <img src="assets/images/cilent-img.jpg" alt="cilent-img">
                        </figure>
                        <h5>John Michael</h5>
                        <span class="d-block">Engineer</span>
                    </div>
                </div>
              </div>
              <div class="carousel-item">
                <div class="testimonial-content text-center">
                    <figure>
                        <img src="assets/images/quote-icon.png" alt="quote-icon">
                    </figure>
                    <p>Nemo enim ipsam voluptatem quia voluptas sit as <br>
                        pernatur iaut odite aut fugit, sed quia consequunt ur magni dolores eos qui rati <br> one voluptatem sequi dolor porro quisquam nesciunt.</p>
                    <div class="auther-con">
                        <figure>
                            <img src="assets/images/cilent-img.jpg" alt="cilent-img">
                        </figure>
                        <h5>John Michael</h5>
                        <span class="d-block">Engineer</span>
                    </div>
                </div>
              </div>
              <div class="carousel-item">
                <div class="testimonial-content text-center">
                    <figure>
                        <img src="assets/images/quote-icon.png" alt="quote-icon">
                    </figure>
                    <p>Nemo enim ipsam voluptatem quia voluptas sit as <br>
                        pernatur iaut odite aut fugit, sed quia consequunt ur magni dolores eos qui rati <br> one voluptatem sequi dolor porro quisquam nesciunt.</p>
                    <div class="auther-con">
                        <figure>
                            <img src="assets/images/cilent-img.jpg" alt="cilent-img">
                        </figure>
                        <h5>John Michael</h5>
                        <span class="d-block">Engineer</span>
                    </div>
                </div>
              </div>
            </div>
            <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
              <span class="carousel-control-prev-icon" aria-hidden="true">
                  <img src="assets/images/long-arrow-lft.png" alt="long-arrow-lft">
              </span>
            </a>
            <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
              <span class="carousel-control-next-icon" aria-hidden="true">
                <img src="assets/images/long-arrow-rt.png" alt="long-arrow-rt">
              </span>
            </a>
          </div>
    </div>
</section>
<!--  -->
<!-- logos section-->

<!-- logos section-->
<!-- BLOG POST SECTION -->

<a id="button" class="show"></a>
@endsection
