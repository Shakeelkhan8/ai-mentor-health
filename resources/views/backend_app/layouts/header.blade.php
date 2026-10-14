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

      @if(config('mentor.features.weekly_plans'))
      <li class="menu-item {{ Request::is('plans/weekly') ? 'active' : '' }}">
        <a href="{{ route('plans.weekly.show') }}" class="menu-link">
          <i class="menu-icon tf-icons ti ti-calendar-event"></i>
          <div>Weekly Plan</div>
        </a>
      </li>
      @endif

      @if(config('mentor.features.subscriptions'))
      <li class="menu-item {{ Request::is('billing*') ? 'active' : '' }}">
        <a href="{{ route('billing.show') }}" class="menu-link">
          <i class="menu-icon tf-icons ti ti-credit-card"></i>
          <div>
            Billing
            @if(! auth()->user()?->activeSubscription())
              <span class="badge bg-label-primary ms-1">Pro</span>
            @endif
          </div>
        </a>
      </li>
      @endif

      <!-- Academy menu end -->

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
