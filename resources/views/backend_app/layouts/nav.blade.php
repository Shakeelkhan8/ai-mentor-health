
@php
    $user=Auth::user();
    $notifications=showNotifications();
@endphp
<style>
   @media(max-width:768px){

        .mob-view {
            margin-top: -39px!important;
        }
    }
</style>
<nav
class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme d-block"
id="layout-navbar">
<div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
  <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
    <i class="ti ti-menu-2 ti-sm"></i>
  </a>
</div>

<div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse" >

  <ul class="navbar-nav flex-row align-items-center ms-auto mob-view">
    <!-- Language -->

    <!--/ Language -->

    <!-- Style Switcher -->
    <li class="nav-item dropdown-style-switcher dropdown me-2 me-xl-0">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
          <i class="ti ti-md"></i>
        </a>
        <ul class="dropdown-menu dropdown-menu-end dropdown-styles">
          <li>
            <a class="dropdown-item" href="javascript:void(0);" data-theme="light">
              <span class="align-middle"><i class="ti ti-sun me-2"></i>Light</span>
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="javascript:void(0);" data-theme="dark">
              <span class="align-middle"><i class="ti ti-moon me-2"></i>Dark</span>
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="javascript:void(0);" data-theme="system">
              <span class="align-middle"><i class="ti ti-device-desktop me-2"></i>System</span>
            </a>
          </li>
        </ul>
      </li>
    <!-- / Style Switcher-->

    <!-- Quick links  -->

    <!-- Quick links -->

    <!-- Notification -->
      <!-- Notification -->
       <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-1">
          <a
            class="nav-link dropdown-toggle hide-arrow"
            href="javascript:void(0);"
            data-bs-toggle="dropdown"
            data-bs-auto-close="outside"
            aria-expanded="false">
            <i class="ti ti-bell ti-md"></i>
            <span class="badge bg-danger rounded-pill badge-notifications">{{ $notifications->count() }}</span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end py-0">
            <li class="dropdown-menu-header border-bottom">
              <div class="dropdown-header d-flex align-items-center py-3">
                <h5 class="text-body mb-0 me-auto">Notification</h5>
                <a
                  href="javascript:void(0)"
                  class="dropdown-notifications-all text-body"
                  data-bs-toggle="tooltip"
                  data-bs-placement="top"
                  title="Mark all as read"
                  ><i class="ti ti-mail-opened fs-4"></i
                ></a>
              </div>
            </li>
            <li class="dropdown-notifications-list scrollable-container">
              <ul class="list-group list-group-flush">
                @foreach ($notifications as $item)
                <li class="list-group-item list-group-item-action dropdown-notifications-item">
                  <div class="d-flex">
                    <div class="flex-shrink-0 me-3">
                      <div class="avatar">
                        <img src="{{ asset('assets/users/'.$item->user->img) }}" onerror="this.src=''https://media.istockphoto.com/id/1337144146/vector/default-avatar-profile-icon-vector.jpg?s=612x612&w=0&k=20&c=BIbFwuv7FxTWvh5S3vB6bkT0Qv8Vn8N5Ffseq84ClGI=" alt class="h-auto rounded-circle" />
                      </div>
                    </div>
                    <div class="flex-grow-1">
                      <h6 class="mb-1">{{ $item->title }}</h6>
                      <p class="mb-0">{{ $item->msg }}</p>
                      <small class="text-muted">1h ago</small>
                    </div>
                    <div class="flex-shrink-0 dropdown-notifications-actions">
                      <a href="javascript:void(0)" class="dropdown-notifications-read"
                        ><span class="badge badge-dot"></span
                      ></a>
                      <a href="javascript:void(0)" class="dropdown-notifications-archive"
                        ><span class="ti ti-x"></span
                      ></a>
                    </div>
                  </div>
                </li>
                @endforeach
             
        
              </ul>
            </li>
            <li class="dropdown-menu-footer border-top">
              <a
                href="javascript:void(0);"
                class="dropdown-item d-flex justify-content-center text-primary p-2 h-px-40 mb-1 align-items-center">
                View all notifications
              </a>
            </li>
          </ul>
        </li> 
   

    <!-- User -->
    <li class="nav-item navbar-dropdown dropdown-user dropdown">
      <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
        <div class="avatar avatar-online">
          <img src="{{asset("assets/users/".$user->img )}}" onerror="this.src='https://macromissionary.com/wp-content/uploads/2021/10/dummy-avatar-2.jpg'" alt    class="h-auto rounded-circle" />
        </div>
      </a>
      <ul class="dropdown-menu dropdown-menu-end">
        <li>
          <a class="dropdown-item" >
            <div class="d-flex">
              <div class="flex-shrink-0 me-3">
                <div class="avatar avatar-online">
                  <img src="{{asset("assets/users/".$user->img )}}" onerror="this.src='https://macromissionary.com/wp-content/uploads/2021/10/dummy-avatar-2.jpg'" alt class="h-auto rounded-circle" />
                </div>
              </div>
              <div class="flex-grow-1">
                <span class="fw-medium d-block">{{$user->name}}</span>
                <small class="text-muted">{{$user->role}}</small>
              </div>
            </div>
          </a>
        </li>
        <li>
          <div class="dropdown-divider"></div>
        </li>
        <li>
          <a class="dropdown-item" href="{{route('edit_profile')}}">
            <i class="ti ti-user-check me-2 ti-sm"></i>
            <span class="align-middle">My Profile</span>
          </a>
        </li>
        <li>
          <a class="dropdown-item" href="pages-account-settings-account.html">
            <i class="ti ti-settings me-2 ti-sm"></i>
            <span class="align-middle">Settings</span>
          </a>
        </li>


        <li>
          <a class="dropdown-item" href="{{route('logout')}}" >
            <i class="ti ti-logout me-2 ti-sm"></i>
            <span class="align-middle">Log Out</span>
          </a>
        </li>
      </ul>
    </li>
    <!--/ User -->
  </ul>
</div>

<!-- Search Small Screens -->
<div class="navbar-search-wrapper search-input-wrapper d-none">
  <input
    type="text"
    class="form-control search-input container-xxl border-0"
    placeholder="Search..."
    aria-label="Search..." />
  <i class="ti ti-x ti-sm search-toggler cursor-pointer"></i>
</div>
</nav>
