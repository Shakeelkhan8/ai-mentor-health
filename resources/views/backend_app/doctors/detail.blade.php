@extends('backend_app.layouts.template')
@section('content')

<div class="layout-page">
    <!-- Navbar -->
    @include('backend_app.layouts.nav')


        <!-- Content wrapper -->
        <div class="content-wrapper">
          <!-- Content -->

          <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="py-3 mb-4"><span class="text-muted fw-light">Doctor / View /</span> Profile</h4>
            <div class="row">
              <!-- User Sidebar -->
              <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
                <!-- User Card -->
                <div class="card mb-4">
                  <div class="card-body">
                    <div class="user-avatar-section">
                      <div class="d-flex align-items-center flex-column">
                        <img
                          class="img-fluid rounded mb-3 pt-1 mt-4"
                          src="{{ asset('assets/doctors/' . $user->img) }}" onerror="this.src='https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT45gcJLX6J9Wlyr4rFHA3beqZJbTyCvo_0whWJegVnZQ&s'"
                          height="100"
                          width="100"
                          alt="User avatar" />
                        <div class="user-info text-center">
                          <h4 class="mb-2">{{$user->name}}</h4>
                          <span class="badge bg-label-secondary mt-1 text-capitalize"></span>
                        </div>
                      </div>
                    </div>
                    <div class="d-flex justify-content-around flex-wrap mt-3 pt-3 pb-4 border-bottom">
                      <div class="d-flex align-items-start me-4 mt-3 gap-2">
                        <span class="badge bg-label-primary p-2 rounded"><i class="ti ti-checkbox ti-sm"></i></span>
                        <div>
                          <p class="mb-0 fw-medium">Pkr:{{ $user->price }}</p>
                          <small>Appointment Price</small>
                        </div>
                      </div>
                      <div class="d-flex align-items-start mt-3 gap-2">
                        <span class="badge bg-label-primary p-2 rounded"><i class="ti ti-briefcase ti-sm"></i></span>
                        <div>
                          <p class="mb-0 fw-medium">20</p>
                          <small>Total Appointments</small>
                        </div>
                      </div>
                    </div>
                    <p class="mt-4 small text-uppercase text-muted">Details</p>
                    <div class="info-container">
                      <ul class="list-unstyled">
                        <li class="mb-2">
                          <span class="fw-medium me-1">Username:</span>
                          <span>{{$user->name}}</span>
                        </li>
                        <li class="mb-2 pt-1">
                          <span class="fw-medium me-1">Email:</span>
                          <span>{{$user->email}}</span>
                        </li>
                        <li class="mb-2 pt-1">
                          <span class="fw-medium me-1">Status:</span>
                          <span class="badge bg-label-success">Active</span>
                        </li>

                        <li class="mb-2 pt-1">
                          <span class="fw-medium me-1">Designation</span>
                          <span>{{$user->designation}}</span>
                        </li>
                        <li class="mb-2 pt-1">
                          <span class="fw-medium me-1">Contact:</span>
                          <span>{{$user->phone_no}}</span>
                        </li>
                        <li class="mb-2 pt-1">
                          <span class="fw-medium me-1">Address:</span>
                          <span>{{$user->address}}</span>
                        </li>
                        <li class="pt-1">
                          <span class="fw-medium me-1">Country:</span>
                          <span>{{$user->country}}</span>
                        </li>
                       
                      </ul>
                    </div>
                  </div>
                </div>
                <!-- /User Card -->
                <!-- Plan Card -->
              
                <!-- /Plan Card -->
              </div>
              <div class="col-xl-8">
                <div class="card mb-4">
                  <div class="card-body">
                    @forelse($user->feedbacks as $item)
                    <div class="d-flex px-3 mt-4">
                      <div>
                        <img 
                          height="50"
                          width="50"
                          class=" rounded-circle" 
                          src="{{ asset('assets/users/'.$item->img) }}" 
                          onerror="this.src='https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT45gcJLX6J9Wlyr4rFHA3beqZJbTyCvo_0whWJegVnZQ&s'" 
                          alt=""
                        >
                      </div>
                      <div class="ms-3">
                        <h6 class="mb-0">{{ $item->user->name }}</h6>
                        <p>{{ $item->message }}</p>
                        <div>
                          {{-- Loop for filled stars --}}
                          @for ($i = 0; $i < $item->stars; $i++)
                            <i class="fa-solid fa-star text-warning"></i>
                          @endfor
              
                          {{-- Loop for empty stars until total of 5 --}}
                          @for ($i = $item->stars; $i < 5; $i++)
                            <i class="fa-solid fa-star"></i>
                          @endfor
                        </div>
                      </div>
                    </div>
                    @empty
                    <div>No Reviews Yet</div>
                    @endforelse
                  </div>
                </div>
              </div>
              
              
              

              <!--/ User Sidebar -->

              <!-- User Content -->
              {{-- <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
                <!-- User Pills -->
                <ul class="nav nav-pills flex-column flex-md-row mb-4">
                  <li class="nav-item">
                    <a class="nav-link active" href="javascript:void(0);"
                      ><i class="ti ti-user-check ti-xs me-1"></i>Account</a
                    >
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="app-user-view-security.html"
                      ><i class="ti ti-lock ti-xs me-1"></i>Security</a
                    >
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="app-user-view-billing.html"
                      ><i class="ti ti-currency-dollar ti-xs me-1"></i>Billing & Plans</a
                    >
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="app-user-view-notifications.html"
                      ><i class="ti ti-bell ti-xs me-1"></i>Notifications</a
                    >
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="app-user-view-connections.html"
                      ><i class="ti ti-link ti-xs me-1"></i>Connections</a
                    >
                  </li>
                </ul>
                <!--/ User Pills -->

                <!-- Project table -->
                <div class="card mb-4">
                  <h5 class="card-header">User's Projects List</h5>
                  <div class="table-responsive mb-3">
                    <table class="table datatable-project border-top">
                      <thead>
                        <tr>
                          <th></th>
                          <th>Project</th>
                          <th class="text-nowrap">Total Task</th>
                          <th>Progress</th>
                          <th>Hours</th>
                        </tr>
                      </thead>
                    </table>
                  </div>
                </div>
                <!-- /Project table -->

                <!-- Activity Timeline -->
                <div class="card mb-4">
                  <h5 class="card-header">User Activity Timeline</h5>
                  <div class="card-body pb-0">
                    <ul class="timeline mb-0">
                      <li class="timeline-item timeline-item-transparent">
                        <span class="timeline-point timeline-point-primary"></span>
                        <div class="timeline-event">
                          <div class="timeline-header mb-1">
                            <h6 class="mb-0">12 Invoices have been paid</h6>
                            <small class="text-muted">12 min ago</small>
                          </div>
                          <p class="mb-2">Invoices have been paid to the company</p>
                          <div class="d-flex">
                            <a href="javascript:void(0)" class="me-3">
                              <img
                                src="../../assets/img/icons/misc/pdf.png"
                                alt="PDF image"
                                width="15"
                                class="me-2" />
                              <span class="fw-medium text-heading">invoices.pdf</span>
                            </a>
                          </div>
                        </div>
                      </li>
                      <li class="timeline-item timeline-item-transparent">
                        <span class="timeline-point timeline-point-warning"></span>
                        <div class="timeline-event">
                          <div class="timeline-header mb-1">
                            <h6 class="mb-0">Client Meeting</h6>
                            <small class="text-muted">45 min ago</small>
                          </div>
                          <p class="mb-2">Project meeting with john @10:15am</p>
                          <div class="d-flex flex-wrap">
                            <div class="avatar me-3">
                              <img src="../../assets/img/avatars/3.png" alt="Avatar" class="rounded-circle" />
                            </div>
                            <div>
                              <h6 class="mb-0">Lester McCarthy (Client)</h6>
                              <small>CEO of Pixinvent</small>
                            </div>
                          </div>
                        </div>
                      </li>
                      <li class="timeline-item timeline-item-transparent">
                        <span class="timeline-point timeline-point-info"></span>
                        <div class="timeline-event">
                          <div class="timeline-header mb-1">
                            <h6 class="mb-0">Create a new project for client</h6>
                            <small class="text-muted">2 Day Ago</small>
                          </div>
                          <p class="mb-2">5 team members in a project</p>
                          <div class="d-flex align-items-center avatar-group">
                            <div
                              class="avatar pull-up"
                              data-bs-toggle="tooltip"
                              data-popup="tooltip-custom"
                              data-bs-placement="top"
                              title="Vinnie Mostowy">
                              <img src="../../assets/img/avatars/5.png" alt="Avatar" class="rounded-circle" />
                            </div>
                            <div
                              class="avatar pull-up"
                              data-bs-toggle="tooltip"
                              data-popup="tooltip-custom"
                              data-bs-placement="top"
                              title="Marrie Patty">
                              <img src="../../assets/img/avatars/12.png" alt="Avatar" class="rounded-circle" />
                            </div>
                            <div
                              class="avatar pull-up"
                              data-bs-toggle="tooltip"
                              data-popup="tooltip-custom"
                              data-bs-placement="top"
                              title="Jimmy Jackson">
                              <img src="../../assets/img/avatars/9.png" alt="Avatar" class="rounded-circle" />
                            </div>
                            <div
                              class="avatar pull-up"
                              data-bs-toggle="tooltip"
                              data-popup="tooltip-custom"
                              data-bs-placement="top"
                              title="Kristine Gill">
                              <img src="../../assets/img/avatars/6.png" alt="Avatar" class="rounded-circle" />
                            </div>
                            <div
                              class="avatar pull-up"
                              data-bs-toggle="tooltip"
                              data-popup="tooltip-custom"
                              data-bs-placement="top"
                              title="Nelson Wilson">
                              <img src="../../assets/img/avatars/4.png" alt="Avatar" class="rounded-circle" />
                            </div>
                          </div>
                        </div>
                      </li>
                      <li class="timeline-item timeline-item-transparent border-transparent">
                        <span class="timeline-point timeline-point-success"></span>
                        <div class="timeline-event">
                          <div class="timeline-header mb-1">
                            <h6 class="mb-0">Design Review</h6>
                            <small class="text-muted">5 days Ago</small>
                          </div>
                          <p class="mb-0">Weekly review of freshly prepared design for our new app.</p>
                        </div>
                      </li>
                    </ul>
                  </div>
                </div>
                <!-- /Activity Timeline -->

                <!-- Invoice table -->
                <div class="card mb-4">
                  <div class="table-responsive mb-3">
                    <table class="table datatable-invoice border-top">
                      <thead>
                        <tr>
                          <th></th>
                          <th>ID</th>
                          <th><i class="ti ti-trending-up text-secondary"></i></th>
                          <th>Total</th>
                          <th>Issued Date</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                    </table>
                  </div>
                </div>
                <!-- /Invoice table -->
              </div> --}}
              <!--/ User Content -->
            </div>

          </div>
          <!-- / Content -->

          @include('backend_app.layouts.footer')

          <div class="content-backdrop fade"></div>
        </div>
        <!-- Content wrapper -->
      </div>



@endsection

