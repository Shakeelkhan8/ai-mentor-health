@extends('backend_app.layouts.template')
@section('content')

<div class="layout-page">
    <!-- Navbar -->
    @include('backend_app.layouts.nav')
    <!-- / Navbar -->
    <!-- Content wrapper -->
    <div class="content-wrapper">
      <!-- Content -->
      <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-dark fw-dark">Doctors List</h4>
        <div class="row mb-3">
            <div class="col-12">
                <button class="btn add-new btn-primary mb-3 mb-md-0 float-end" tabindex="0" aria-controls="DataTables_Table_0" type="button" " data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser" ><span><i class="ti ti-plus me-0 me-sm-1 ti-xs"></i> Add New Doctor</span></button>
            </div>
        </div>
        <!-- DataTable with Buttons -->
        <div class="card">
            <div class="table-responsive text-nowrap">
                {{-- <a href="{{route('add-files')}}" class="btn btn-primary float-end mt-3 mx-3">Add New File</a> --}}
                <table class="table">
                  <thead>
                    <tr class="text-nowrap">
                        <th>Id</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone no</th>
                        <th>Designation</th>
                        <th>Featured</th>
                        <th>Action</th>
                    </tr>
                    <tbody id="table-body">
                        @foreach ($data as $key=>$user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td style="width:20%;">
                            <div class="avatar avatar-sm pull-up">
                                <a href="{{route('show-doctor',['id'=>$user->id])}}">
                                <img class="rounded-circle me-1 border" src="{{ asset('assets/doctors/' . $user->img) }}" onerror="this.src='https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT45gcJLX6J9Wlyr4rFHA3beqZJbTyCvo_0whWJegVnZQ&s'" alt="Avatar">
                                {{ $user->name }}
                            </a>
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone_no }}</td>
                        <td>{{ $user->designation }}</td>
                        <td><input type="checkbox"></td>

                        <td>  <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                              <i class="ti ti-dots-vertical"></i>
                            </button>
                            <div class="dropdown-menu">
                              <a class="dropdown-item"  data-bs-toggle="modal" data-bs-target="#exampleModal"
                                ><i class="ti ti-plus me-1"></i> Add Feedback</a
                              >
                              <a class="dropdown-item"  tabindex="0" data-bs-toggle="modal" data-bs-target="#edit_permission_{{$key}}"
                                ><i class="ti ti-pencil me-1"></i> Edit</a
                              >
                              <a class="dropdown-item"  href="{{route('delete-doctor',['id'=>$user->id])}}"
                                ><i class="ti ti-trash me-1"></i> Delete</a
                              >
                            </div>
                          </div></td>


                          <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h1 class="modal-title fs-5" id="exampleModalLabel">Give FeedBack to {{ $user->name }}</h1>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                  <form method="POST" action="{{ route('add-feedback') }}" >
                                    @csrf
                                  <input type="hidden" name="doctor_id" id="doctor-id">
                                  <div class="mb-3">
                                    <input type="hidden" name="doctor_id" value="{{ $user->id }}">
                                      <label for="message" class="form-label">Message</label>
                                      <textarea class="form-control" name="message" id="message" rows="3" required></textarea>
                                  </div>
                                  <div class="mb-3">
                                      <label for="stars" class="form-label">Rating</label>
                                      <select class="form-select" name="stars" id="stars" required>
                                          <option value="" selected disabled>Select Stars</option>
                                          <option value="1">1 Star</option>
                                          <option value="2">2 Stars</option>
                                          <option value="3">3 Stars</option>
                                          <option value="4">4 Stars</option>
                                          <option value="5">5 Stars</option>
                                      </select>
                                  </div>
                              </div>
                              <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                  <button type="submit" class="btn btn-primary">Submit Feedback</button>
                              </div>
                            </form>
                              </div>
                            </div>
                          </div>

                          <div class="modal fade" id="edit_permission_{{$key}}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                              <div class="modal-content p-3">
                                <button
                                  type="button"
                                  class="btn-close btn-pinned"
                                  data-bs-dismiss="modal"
                                  aria-label="Close"></button>
                                <div class="modal-body">
                                  <div class="text-center mb-4">
                                    <h3 class="mb-2">Update Doctor</h3>
                                    <p class="text-muted">Update the existing Doctor information</p>
                                  </div>
                                  <form action="{{route('update-doctor',['id'=>$user->id])}}" method="POST">
                                    @csrf

                                    <div class="mb-3">
                                        <label for="">Name</label>
                                        <input type="text" name="name" value="{{$user->name}}" placeholder="Enter Full Name" class="form-control" />
                                    </div>
                                    <div class="mb-3">
                                        <label for="">Email</label>
                                        <input type="text" name="email" value="{{$user->email}}"  placeholder="Enter Email" class="form-control" />
                                    </div>
                                    <div class="mb-3">
                                        <label for="">Phone no</label>
                                        <input type="text" name="phone_no" value="{{$user->phone_no}}"  placeholder="Phone no" class="form-control" />
                                    </div>
                                    <div class="mb-3">
                                        <label for="">Designation</label>
                                        <input type="text" name="designation" value="{{$user->designation}}"  placeholder="Enter Designation" class="form-control" />
                                    </div>
                                    <div class="mb-3">
                                        <label for="">Country</label>
                                        <input type="text" name="country" value="{{$user->country}}"  placeholder="Enter Country" class="form-control" />
                                    </div>
                                    <div class="mb-3">
                                        <label for="">City</label>
                                        <input type="text" name="city" value="{{$user->city}}"  placeholder="Enter City" class="form-control" />
                                    </div>
                                    <div class="mb-3">
                                        <label for="">Address</label>
                                        <input type="text" name="address" value="{{$user->address}}"  placeholder="Enter Address" class="form-control" />
                                    </div>
                                    <div class="mb-3">
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </div>
                                </form>
                                </div>
                              </div>
                            </div>
                          </div>
                    </tr>
                    @endforeach
                </tbody>
                </thead>
               </table>
          </div>
        </div>
        {{-- <div id="paginationContainer" class="float-end mt-3">
            {{$data->links()}}
         </div> --}}
        <!-- Modal to add new record -->

        <!--/ DataTable with Buttons -->
      </div>
      <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel">
        <div class="offcanvas-header">
          <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Add Doctor</h5>
          <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
            <form action="{{route('store-doctor')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                  <label for="">Img</label>
                  <input type="file" name="img" placeholder="Enter Full Name" class="form-control" />
              </div>
                <div class="mb-3">
                    <label for="">Name</label>
                    <input type="text" name="name" placeholder="Enter Full Name" class="form-control" />
                </div>
                <div class="mb-3">
                    <label for="">Email</label>
                    <input type="text" name="email"  placeholder="Enter Email" class="form-control" />
                </div>
                <div class="mb-3">
                    <label for="">Phone no</label>
                    <input type="text" name="phone_no"  placeholder="Phone no" class="form-control" />
                </div>
                <div class="mb-3">
                    <label for="">Designation</label>
                    <input type="text" name="designation"  placeholder="Enter Designation" class="form-control" />
                </div>
                <div class="mb-3">
                    <label for="">Country</label>
                    <input type="text" name="country"  placeholder="Enter Country" class="form-control" />
                </div>
                <div class="mb-3">
                    <label for="">City</label>
                    <input type="text" name="city"  placeholder="Enter City" class="form-control" />
                </div>
                <div class="mb-3">
                    <label for="">Address</label>
                    <input type="text" name="address"  placeholder="Enter Address" class="form-control" />
                </div>
                <div class="mb-3">
                  <label for="">Price</label>
                  <input type="number" name="price"  placeholder="Enter Price" class="form-control" />
              </div>
                <div class="mb-3">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
      </div>

    
      <!-- / Content -->
      <!-- Footer -->
  @include('backend_app.layouts.footer')
      <div class="content-backdrop fade"></div>
    </div>
    <!-- Content wrapper -->
  </div>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

@endsection

