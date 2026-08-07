<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
   @php
       $user=Auth::user();
   @endphp
    <div class="app-brand demo">
      <a href="{{route('dashboard')}}" class="app-brand-link py-5">
        <img class="bg-white rounded-3" src="https://images.squarespace-cdn.com/content/v1/587a592b3a0411c502816bd8/1484477606857-VZ7SRNMLU8ERLGGL5WSA/MindMentor_Logo_Black_02.png" style="width:150px;" class="m-auto d-block">

      </a>

      <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
        <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
        <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
      </a>
    </div>

    <div class="menu-inner-shadow"></div>
    <ul class="menu-inner py-1">
      <!-- Dashboards -->
      <li class="menu-item {{ Request::is('dashboard') ?'active':'' }}">
        <a href="{{route('dashboard')}}" class="menu-link" >
          <i class="menu-icon tf-icons ti ti-smart-home"></i>
          <div>Dashboards</div>
        </a>
      </li>

      <!-- Layouts -->
      {{-- <li class="menu-item {{ Request::is('add-files') || Request::is('all-files') ? 'active' : '' }}">

      <li class="menu-item {{ Request::is('add-files') || Request::is('all-files') ? 'open' : '' }}  ">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons ti ti-chart-pie"></i>
          <div >File Modules</div>
        </a>

        <ul class="menu-sub">
          <li class="menu-item {{ Request::is('add-files') ? 'active' : '' }}">
            <a href="{{route('add-files')}}" class="menu-link">
              <div >Add New File</div>
            </a>
          </li>
          <li class="menu-item {{ Request::is('all-files') ? 'active' : '' }}">
            <a href="{{route('all-files')}}" class="menu-link">
              <div >View All Files</div>
            </a>
          </li>

        </ul>
      </li>


      <li class="menu-item {{ Request::is('discount/add-form') || Request::is('discount/all-forms') ? 'open' : '' }} ">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons ti ti-layout-sidebar"></i>
          <div >Discount Forms</div>
        </a>

        <ul class="menu-sub">
          <li class="menu-item {{ Request::is('discount/add-form')  ? 'active' : '' }}">
            <a href="{{route('add-discount-form')}}" class="menu-link">
              <div >Add Forms</div>
            </a>
          </li>
          <li class="menu-item {{ Request::is('discount/all-forms') ? 'active' : '' }}">
            <a href="{{route('all-discount-form')}}" class="menu-link">
              <div >All Forms</div>
            </a>
          </li>

        </ul>
      </li>
      <!-- Front Pages -->
      <li class="menu-item {{ Request::is('all-dealers') || Request::is('add-dealer') ? 'open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons ti ti-files"></i>
          <div >Sales Partners</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{ Request::is('all-dealers')  ? 'active' : '' }}">
            <a href="{{route('all-dealer')}}" class="menu-link">
              <div >All Dealers</div>
            </a>
          </li>
          <li class="menu-item {{ Request::is('add-dealer')  ? 'active' : '' }}">
            <a href="{{route('add-dealer')}}" class="menu-link" >
              <div >Add Dealers</div>
            </a>
          </li>

        </ul>
      </li>



      {{-- <!-- Apps & Pages --> --}}

      @if(config('mentor.features.care_marketplace') && $user->role === "admin")
      <li class="menu-item {{ Request::is('all-clients') || Request::is('add-client') ? 'open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons ti ti-id"></i>
          <div >Doctors</div>
        </a>
        <ul class="menu-sub">
          
          <li class="menu-item {{ Request::is('all-clients') ? 'active' : '' }}">
            <a href="{{route('all-doctors')}}" class="menu-link">
              <div >All Doctors</div>
            </a>
          </li>
          <li class="menu-item {{ Request::is('add-client') ? 'active' : '' }}">
            <a href="{{route('create-doctor')}}" class="menu-link" >
              <div >Add Doctor</div>
            </a>
          </li>
        </ul>
      </li>
      @endif
      @if(config('mentor.features.care_marketplace'))
      <li class="menu-item {{ Request::is('chatbot') ? 'active' : '' }}">
        <a href="{{ route('appointment.index') }}" class="menu-link">
          <i class="menu-icon tf-icons ti ti-brand-tabler"></i>
          <div >Make Appointment</div>
        </a>
      </li>
      <li class="menu-item {{ Request::is('chatbot') ? 'active' : '' }}">
        <a href="{{ route('appointment.all') }}" class="menu-link">
          <i class="menu-icon tf-icons ti ti-brand-tabler"></i>
          <div >Appointments</div>
        </a>
      </li>
      @endif
      <li class="menu-header small text-uppercase">
        <span class="menu-header-text">AI Mentor</span>
      </li>

      @if(!($user->profile?->hasCompletedOnboarding() ?? false))
      <li class="menu-item {{ Request::is('onboarding') ? 'active' : '' }}">
        <a href="{{ route('onboarding.show') }}" class="menu-link">
          <i class="menu-icon tf-icons ti ti-user-check"></i>
          <div>Complete setup</div>
        </a>
      </li>
      @endif

      @if(config('mentor.features.ai_coach'))
      <li class="menu-item {{ Request::is('ai/chatbot') || Request::is('ai/coach') || Request::is('chatbot') ? 'active' : '' }}">
        <a href="{{route('chatbot')}}" class="menu-link">
          <i class="menu-icon tf-icons ti ti-brand-tabler"></i>
          <div >AI Coach</div>
        </a>
      </li>
      @endif

      <!-- Academy menu end -->


{{--
      <li class="menu-item {{ Request::is('all-forms')     ? 'active' : '' }}">
        <a href="{{route('all-forms')}}" class="menu-link ">
          <i class="menu-icon tf-icons ti ti-file-description"></i>
          <div>Customer Query</div>
        </a>

      </li>
      <li class="menu-item {{ Request::is('all-partners') ? 'active' : '' }}">
        <a href="{{route('all-partners')}}" class="menu-link ">
          <i class="menu-icon tf-icons ti ti-users"></i>
          <div>Affiliated Sales Partners</div>
        </a>

      </li> --}}
      <li class="menu-header small text-uppercase">
        <span class="menu-header-text">Advance Options</span>
      </li>

      <li class="menu-item {{ Request::is('edit-profile') ? 'active' : '' }}">
        <a href="{{route('edit_profile')}}" class="menu-link">
          <i class="menu-icon tf-icons ti ti-mail"></i>
          <div >Edit porfile</div>
        </a>
      </li>

      <!-- Academy menu end -->


      @if($user->role === "admin")
      <li class="menu-item">
        <a href="{{ route('community-forms') }}" class="menu-link ">
          <i class="menu-icon tf-icons ti ti-settings"></i>
          <div>Community Forms</div>
        </a>
        @endif
      </li>
    </ul>
  </aside>
