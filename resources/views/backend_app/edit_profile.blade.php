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
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Account Settings /</span> Account</h4>
        <div class="row">
          <div class="col-md-12">
            <ul class="nav nav-pills flex-column flex-md-row mb-4">
              <li class="nav-item" id="account" style="cursor: pointer;">
                <a class="nav-link active" id="account_inner"
                  ><i class="ti-xs ti ti-users me-1"></i> Account</a
                >
              </li>
              <li class="nav-item" id="security" style="cursor: pointer;">
                <a class="nav-link" id="security_inner"
                  ><i class="ti-xs ti ti-lock me-1"></i> Security</a
                >
              </li>



            </ul>

            <div class="card mb-4" id="general_setting">
              <h5 class="card-header">Profile Details</h5>
              <!-- Account -->
              <div class="card-body">
                <form  action="{{route('profile.update')}}" method="POST"  enctype="multipart/form-data">
                    @csrf
                <div class="d-flex align-items-start align-items-sm-center gap-4">
                    @if ($user->img ===null)
                    <img
                    src="../../assets/img/avatars/14.png"
                    alt="user-avatar"
                    class="d-block w-px-100 h-px-100 rounded"
                    id="uploadedAvatar" />
                    @else
                    <img
                    src="{{asset('assets/users/'.$user->img)}}"
                    alt="user-avatar"
                    class="d-block w-px-100 h-px-100 rounded"
                    id="uploadedAvatar"
                    style="height: 150px;object-fit:cover;"
                    />
                    @endif

                  <div class="button-wrapper">
                    <label for="upload" class="btn btn-primary me-2 mb-3" tabindex="0">
                      <span class="d-none d-sm-block">Upload new photo</span>
                      <i class="ti ti-upload d-block d-sm-none"></i>
                      <input
                        type="file"
                        id="upload"
                        class="account-file-input"
                        hidden
                        accept="image/png, image/jpeg"
                        name="img"
                        />
                    </label>
                    <button type="button" class="btn btn-label-secondary account-image-reset mb-3">
                      <i class="ti ti-refresh-dot d-block d-sm-none"></i>
                      <span class="d-none d-sm-block">Reset</span>
                    </button>

                    <div class="text-muted">Allowed JPG, GIF or PNG. Max size of 800K</div>
                  </div>
                </div>
              </div>
              <hr class="my-0" />
              <div class="card-body">

                  <div class="row">
                    <div class="mb-3 col-md-6">
                      <label for="firstName" class="form-label">First Name</label>
                      <input
                        class="form-control"
                        type="text"
                        id="firstName"
                        name="name"
                        value="{{$user->name}}"
                        autofocus />
                    </div>

                    <div class="mb-3 col-md-6">
                      <label for="email" class="form-label">E-mail</label>
                      <input
                        class="form-control"
                        type="text"
                        id="email"
                        name="email"
                        value="{{$user->email}}"
                       />
                    </div>
                    <div class="mb-3 col-md-6">
                      <label for="organization" class="form-label">Organization</label>
                      <input
                        type="text"
                        class="form-control"
                        id="organization"
                        name="organization"
                        value="{{$user->organization}}" />
                    </div>
                    <div class="mb-3 col-md-6">
                      <label class="form-label" for="phoneNumber">Phone Number</label>
                      <div class="input-group input-group-merge">
                        <span class="input-group-text">PK (+92)</span>
                        <input
                          type="text"
                          id="phoneNumber"
                          name="phone_no"
                          class="form-control"
                          value="{{$user->phone_no}}" />
                      </div>
                    </div>
                    <div class="mb-3 col-md-6">
                      <label for="address" class="form-label">Address</label>
                      <input type="text" class="form-control" id="address" name="address" value="{{$user->address}}" />
                    </div>

                    <div class="mb-3 col-md-6">
                      <label class="form-label" for="country">Country</label>
                      <select id="country" name="conutry" class="select2 form-select">
                        <option value="">Select</option>
                        <option value="Australia">Australia</option>
                        <option value="Bangladesh">Bangladesh</option>
                        <option value="Belarus">Belarus</option>
                        <option value="Brazil">Brazil</option>
                        <option value="Canada">Canada</option>
                        <option value="China">China</option>
                        <option value="France">France</option>
                        <option value="Germany">Germany</option>
                        <option value="India">India</option>
                        <option value="Indonesia">Indonesia</option>
                        <option value="Israel">Israel</option>
                        <option value="Italy">Italy</option>
                        <option value="Japan">Japan</option>
                        <option value="Korea">Korea, Republic of</option>
                        <option value="Mexico">Mexico</option>
                        <option value="Philippines">Philippines</option>
                        <option value="Russia">Russian Federation</option>
                        <option value="South Africa">South Africa</option>
                        <option value="Thailand">Thailand</option>
                        <option value="Turkey">Turkey</option>
                        <option value="Ukraine">Ukraine</option>
                        <option value="United Arab Emirates">United Arab Emirates</option>
                        <option value="United Kingdom">United Kingdom</option>
                        <option value="United States">United States</option>
                      </select>
                    </div>



                  </div>
                  <div class="mt-2">
                    <button type="submit" class="btn btn-primary me-2">Save changes</button>
                    <button type="reset" class="btn btn-label-secondary">Cancel</button>
                  </div>
                </form>
              </div>
              <!-- /Account -->
            </div>
            <div class="card mb-4" id="password">
                <h5 class="card-header">Change Password</h5>
                <div class="card-body">
                  <form action="{{route('update-password')}}" method="POST">
                    @csrf
                    <div class="row">
                      <div class="mb-3 col-md-6 form-password-toggle">
                        <label class="form-label" for="currentPassword">Current Password</label>
                        <div class="input-group input-group-merge">
                          <input
                            class="form-control"
                            type="password"
                            name="currentPassword"
                            id="currentPassword"
                            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                          <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                        </div>
                        @error('password')
                        <span class="text-danger">{{$message}}</span>
                        @enderror

                      </div>
                    </div>
                    <div class="row">
                      <div class="mb-3 col-md-6 form-password-toggle">
                        <label class="form-label" for="newPassword">New Password</label>
                        <div class="input-group input-group-merge">
                          <input
                            class="form-control"
                            type="password"
                            id="password"
                            name="newPassword"
                            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                          <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                        </div>
                        @error('newPassword')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                      </div>

                      <div class="mb-3 col-md-6 form-password-toggle">
                        <label class="form-label" for="confirmPassword">Confirm New Password</label>
                        <div class="input-group input-group-merge">
                          <input
                            class="form-control"
                            type="password"
                            name="newPassword_confirmation"
                            id="confirmPassword"
                            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                          <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                        </div>
                        @error('confirm_password')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                      </div>
                      <div class="col-12 mb-4">
                        <h6>Password Requirements:</h6>
                        <ul class="ps-3 mb-0">
                          <li class="mb-1">Minimum 8 characters long - the more, the better</li>
                          <li class="mb-1">At least one lowercase character</li>
                          <li>At least one number, symbol, or whitespace character</li>
                        </ul>
                      </div>
                      <div>
                        <button type="submit" class="btn btn-primary me-2">Save changes</button>
                        <button type="reset" class="btn btn-label-secondary">Cancel</button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>








        </div>
      </div>
      <!-- / Content -->

      <!-- Footer -->

      <!-- / Footer -->

      <div class="content-backdrop fade"></div>
    </div>
    <!-- Content wrapper -->
  </div>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script>
$("#password").hide();
$("#account").click(()=>{
    $('#general_setting').show();
    $('#general_setting').show();
    $("#account_inner").addClass('active');
    $("#security_inner").removeClass('active');
    $("#password").hide();
});
$("#security").click(()=>{
    $
    $('#password').show();
    $("#general_setting").hide();
    $("#security_inner").addClass('active');
    $("#account_inner").removeClass('active');
});
</script>
@endsection
