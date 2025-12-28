<!DOCTYPE html>

<html
  lang="en"
  class="dark-style layout-navbar-fixed layout-menu-fixed layout-compact"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="../../assets/">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Ai Mentor</title>

    <meta name="description" content="" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{asset("assets/img/favicon/favicon.ico")}}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />



    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&ampdisplay=swap"
      rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="{{asset("assets/vendor/fonts/fontawesome.css")}}" />
    <link rel="stylesheet" href="{{asset("assets/vendor/fonts/tabler-icons.css")}}" />
    <link rel="stylesheet" href="{{asset("assets/vendor/fonts/flag-icons.css")}}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{asset("assets/vendor/css/rtl/core.css")}}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{asset("assets/vendor/css/rtl/theme-default.css")}}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{asset("assets/css/demo.css")}}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{asset("assets/vendor/libs/node-waves/node-waves.css")}}" />
    <link rel="stylesheet" href="{{asset("assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css")}}" />
    <link rel="stylesheet" href="{{asset("assets/vendor/libs/typeahead-js/typeahead.css")}}" />
    <link rel="stylesheet" href="{{asset("assets/vendor/libs/apex-charts/apex-charts.css")}}" />
    <link rel="stylesheet" href="{{asset("assets/vendor/libs/swiper/swiper.css")}}" />
    <link rel="stylesheet" href="{{asset("assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css")}}" />
    <link rel="stylesheet" href="{{asset("assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css")}}" />
    <link rel="stylesheet" href="{{asset("assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css")}}" />
    <link rel="stylesheet" href="{{asset("assets/vendor/libs/select2/select2.css")}}" />
    <link rel="stylesheet" href="{{asset("assets/vendor/libs/tagify/tagify.css")}}" />
    <!-- Page CSS -->
    <link rel="stylesheet" href="{{asset("assets/vendor/css/pages/cards-advance.css")}}" />
    <link rel="stylesheet" href="{{asset("assets/vendor/css/pages/page-profile.css")}}" />

    <!-- Helpers -->
    <script src="{{asset("assets/vendor/js/helpers.js")}}"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    <script src="{{asset("assets/vendor/js/template-customizer.js")}}"></script>
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{asset("assets/js/config.js")}}"></script>
  </head>
  <style>
    .btn-primary{
        color: black;
    }
    .nav-pills .nav-link.active, .nav-pills .nav-link.active:hover, .nav-pills .nav-link.active:focus {
    background-color: #00FCDB;
    color: black;
}
  </style>


  <body >
    <div id="preloader"  class="spinner-grow text-success" style="position:absolute;" role="status">
        <span class="visually mx-5">Loading...</span>
      </div>
    <div id="wrapper" style="display:none;">
    @if(session('success'))
    <div id="success" class="alert alert-success d-flex align-items-center" style="
    top: 21px;
    position: absolute;
    width: 50%;
    z-index: 9999;
    left: 30%;
"  role="alert">
        <span class="alert-icon text-success me-2">
          <i class="ti ti-check ti-xs"></i>
        </span>
        {{ session('success') }}
      </div>
    @endif
    @if(session('error'))
    <div id="error" class="alert alert-danger d-flex align-items-center" style="
    top: 21px;
    position: absolute;
    width: 50%;
    z-index: 9999;
    left: 30%;
"  role="alert">
        <span class="alert-icon text-danger me-2">
          <i class="ti ti-ban ti-xs"></i>
        </span>
        {{ session('error') }}
      </div>
    @endif

    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">



        <!-- Menu -->
@include('backend_app.layouts.header')
        <!-- / Menu -->
@yield('content')

      </div>

      <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>

      <!-- Drag Target Area To SlideIn Menu On Small Screens -->
      <div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->


</div>
    <script src="{{asset("assets/vendor/libs/jquery/jquery.js" )}}"></script>
    <script src="{{asset("assets/vendor/libs/popper/popper.js" )}}"></script>
    <script src="{{asset("assets/vendor/js/bootstrap.js" )}}"></script>
    <script src="{{asset("assets/vendor/libs/node-waves/node-waves.js" )}}"></script>
    <script src="{{asset("assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js" )}}"></script>
    <script src="{{asset("assets/vendor/libs/hammer/hammer.js" )}}"></script>
    <script src="{{asset("assets/vendor/libs/i18n/i18n.js" )}}"></script>
    <script src="{{asset("assets/vendor/libs/typeahead-js/typeahead.js" )}}"></script>
    <script src="{{asset("assets/vendor/js/menu.js" )}}"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="{{asset("assets/vendor/libs/apex-charts/apexcharts.js" )}}"></script>
    <script src="{{asset("assets/vendor/libs/swiper/swiper.js" )}}"></script>
    <script src="{{asset("assets/vendor/libs/select2/select2.js")}}"></script>
    <script src="{{asset("assets/vendor/libs/tagify/tagify.js")}}"></script>
    <script src="{{asset("assets/vendor/libs/bootstrap-select/bootstrap-select.js")}}"></script>
    <script src="{{asset("assets/vendor/libs/typeahead-js/typeahead.js")}}"></script>
    <script src="{{asset("assets/vendor/libs/bloodhound/bloodhound.js")}}"></script>

    <!-- Main JS -->
    <script src="{{asset("assets/js/main.js" )}}"></script>


    <!-- Page JS -->

    <script src="{{asset("assets/js/pages-auth.js")}}"></script>
    <script src="{{asset("assets/js/forms-selects.js")}}"></script>
    <script src="{{asset("assets/js/forms-tagify.js")}}"></script>
    <script src="{{asset("assets/js/forms-typeahead.js")}}"></script>


<script>
    $("#wrapper").show();
    $("#preloader").hide();
  document.addEventListener('DOMContentLoaded', function() {


    setTimeout(function() {
        var successDiv = document.getElementById('success');
        if (successDiv) {
            successDiv.style.display = 'none';
        }
    }, 3000);

    setTimeout(function() {
        var errorDiv = document.getElementById('error');
        if (errorDiv) {
            errorDiv.style.display = 'none';
        }
    }, 3000);
});

</script>


  </body>
</html>
