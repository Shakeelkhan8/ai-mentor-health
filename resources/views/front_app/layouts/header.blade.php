<header class="navbar-main-sec index2-navbar-sec w-100 float-left">
    <div class="wrapper">
        <nav class="navbar navbar-expand-lg navbar-light p-0">
            <a class="navbar-brand" href="index01.html">
                <img class="bg-white rounded-3" src="https://images.squarespace-cdn.com/content/v1/587a592b3a0411c502816bd8/1484477606857-VZ7SRNMLU8ERLGGL5WSA/MindMentor_Logo_Black_02.png" style="width:200px;" class="m-auto d-block">
            </a>
            <button class="navbar-toggler collapsed" type="button" data-toggle="collapse"
            data-target="#navbarToggle" aria-controls="navbarToggle" aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            <span class="navbar-toggler-icon"></span>
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarToggle">
            <ul class="navbar-nav mr-auto my-2 my-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="{{route('home')}}">Home</a>
                </li>
                  <li class="nav-item">
                    <a class="nav-link" href="{{route('about')}}">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{route('services')}}">Service</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{route('pricing')}}">Pricing</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{route('faqs')}}">FAQS</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{route('contact')}}">Contact</a>
                </li>
            </ul>
            <form class="d-flex">
                <ul class="mb-0 list-unstyled d-flex align-items-center navbar-right-box">

                    <li>
                        <a class="login-btn" type="submit" href="{{route('login')}}">
                            <img src="assets/images/lock-icon.png" alt="lock-icon"> Login</a>
                    </li>
                </ul>
            </form>
        </div>
        </nav>
    </div>
</header>
