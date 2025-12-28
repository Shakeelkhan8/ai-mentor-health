<section class="footer-main-sec home-footer w-100 float-left padding-bottom ">
    <div class="wrapper2">
        <div class="footer-inner-section">
            <div class="footer-logo footer-box">
                <a href="index.html">
                    <figure>
                        <img src="assets/images/AiMentor-logo.png" alt="AiMentor-logo">
                    </figure>
                </a>
                <p class="mb-0">Copyright © 2024 MindMentor.</p>
                <p>All Rights Reserved.</p>
                <ul class="list-unstyled mb-0">
                    <li><span class="d-block">Social Media</span></li>
                    <li class="d-inline-block ml-0"><a href="https://www.facebook.com/"><i class="fab fa-facebook-f"></i></a></li>
                    <li class="d-inline-block"><a href="https://twitter.com/"><i class="fab fa-twitter"></i></a></li>
                    <li class="d-inline-block"><a href="https://www.instagram.com/"><i class="fab fa-instagram"></i></a></li>
                    <li class="d-inline-block mr-0"><a href="https://pk.linkedin.com/"><i class="fab fa-linkedin-in"></i></a></li>
                </ul>
            </div>
            <div class="footer-box">
                <h4>About</h4>
                <ul class="list-unstyled">
                    <li><a href="about.html">About Company</a></li>
                    <li><a href="#">Our Solutions</a></li>
                    <li><a href="service.html">Our Best Services</a></li>
                    <li class="mb-0"><a href="team.html">Professional Team</a></li>
                </ul>
            </div>
            <div class="footer-box address-box">
                <h4>Address</h4>
                <ul class="list-unstyled">
                    <li class="position-relative"><i class="fas fa-map-marker-alt"></i>H8/4 Szabist University</li>
                    <li class="position-relative"><i class="fas fa-phone-volume"></i> <a href="tel:+61383766284">+00000000</a></li>
                    <li class="mb-0 position-relative"><i class="fas fa-envelope"></i> <a href="mailto:info@designingmedia.com">info@mindmentor@gmail.com</a></li>
                </ul>
            </div>
            <form action="{{route('sendmail')}}" method="POST">
                @csrf
            <div class="footer-box footer-form-box">
                <h4>Newsletter Signup</h4>

                    @csrf
                <ul class="list-unstyled">
                    <li class="position-relative mb-0"><input type="email" name="email" placeholder="Enter Your Email Address"> <button>Send</button></li>
                </ul>


                    <input type="checkbox" class="d-none" id="policy" name="policy" value="Bike">
                    <label for="policy" class="check-box"></label>
                    <label for="policy"> I agree to the <a href="#">Privacy Policy</a>.</label><br>

            </div>
        </form>
        </div>
    </div>
</section>
