@extends('front_app.layouts.template')
@section('content')

<section class="w-100 float-left banner-sec sub-banner about-banner faq-banner">
    <div class="wrapper2">
        <div class="sub-banner-inner-con">
            <div class="sub-banner-img">
                <figure class="mb-0" data-aos="fade-up" data-aos-duration="600">
                    <img src="https://www.medicaldevice-network.com/wp-content/uploads/sites/23/2023/01/shutterstock_2118695231.jpg" class="w-100" alt="sub-banner-img">
                </figure>
            </div>
            <div class="sub-banner-title text-center">
                <h1 class="mb-0" data-aos="fade-up" data-aos-duration="600">FAQ</h1>
            </div>
            <div class="sub-banner-img">
                <figure class="mb-0" data-aos="fade-up" data-aos-duration="600">
                    <img src="https://www.medicaldevice-network.com/wp-content/uploads/sites/23/2023/01/shutterstock_2118695231.jpg" class="w-100" alt="sub-banner-img">
                </figure>
            </div>
        </div>
    </div>

</section>
<!-- banner section -->
<section class="w-100 float-left faq-con faq-page-con pt-0">
    <div class="wrapper2">
        <div class="generic-title text-center">
            <span class="text-uppercase" data-aos="fade-up" data-aos-duration="600">Frequently Asked Questions</span>
            <h2 data-aos="fade-up" data-aos-duration="600">AiMentor Mental Health Support Chatbot FAQs</h2>
        </div>
        <div class="faq-box">
            <div class="faq-content">
                <div id="accordion">
                    <div class="card" data-aos="fade-up" data-aos-duration="600">
                      <div class="card-header" id="headingOne">
                        <h5 class="mb-0">
                          <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            What is AiMentor Mental Health Support Chatbot?
                          </button>
                        </h5>
                      </div>

                      <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            AiMentor Mental Health Support Chatbot is an AI-powered tool specifically designed to offer support, guidance, and resources for mental health concerns among students and young adults. It provides a platform that is easily accessible, completely confidential, and free from judgment.
                        </div>
                      </div>
                    </div>
                    <div class="card" data-aos="fade-up" data-aos-duration="600">
                      <div class="card-header" id="headingTwo">
                        <h5 class="mb-0">
                          <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            How does the AiMentor Chatbot offer support?
                          </button>
                        </h5>
                      </div>
                      <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
                        <div class="card-body">
                            The AiMentor Chatbot offers support by answering questions, providing information on mental health topics, suggesting coping strategies, and guiding users towards professional mental health services when necessary. It aims to be a friendly and understanding first point of contact for individuals who may be hesitant to seek help from a professional.
                        </div>
                      </div>
                    </div>
                    <div class="card" data-aos="fade-up" data-aos-duration="600">
                      <div class="card-header" id="headingThree">
                        <h5 class="mb-0">
                          <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            Is the AiMentor Chatbot confidential?
                          </button>
                        </h5>
                      </div>
                      <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
                        <div class="card-body">
                            Yes, the AiMentor Chatbot ensures complete confidentiality. It does not store any personal data and provides a safe space for users to discuss their mental health concerns.
                        </div>
                      </div>
                    </div>
                    <div class="card mb-0" data-aos="fade-up" data-aos-duration="600">
                        <div class="card-header" id="headingfour">
                          <h5 class="mb-0">
                            <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapsefour" aria-expanded="false" aria-controls="collapsefour">
                                How can I access the AiMentor Chatbot?
                            </button>
                          </h5>
                        </div>
                        <div id="collapsefour" class="collapse" aria-labelledby="headingfour" data-parent="#accordion">
                          <div class="card-body">
                              The AiMentor Chatbot can be accessed through our website or mobile application. Simply visit the platform and initiate a conversation with the chatbot to start receiving support.
                          </div>
                        </div>
                    </div>
                  </div>
            </div>
        </div>
    </div>
</section>


@endsection
