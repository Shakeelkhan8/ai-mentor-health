@extends('backend_app.layouts.template')
@section('content')
@php
    $auth_id=Auth::user()->id;
@endphp
<div class="layout-page">
    <!-- Navbar -->
    @include('backend_app.layouts.nav')
    <!-- / Navbar -->

    <!-- Content wrapper -->
    <div class="content-wrapper">
      <!-- Content -->
      <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-dark fw-dark">Appointment List</h4>

        <!-- DataTable with Buttons -->
        <div class="card">
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                        <tr class="text-nowrap">
                            <th>S.No</th>
                            <th>Name</th>
                         
                        
                            <th>Appointment Date</th>
                            <th>Appointment Time</th>
                          <th>Price</th>
                        </tr>
                    </thead>
                    <tbody id="table-body">
                    @foreach ($data as $key => $item)
                        <tr>
                            <td>{{ $key}}</td>
                            <td style="width:20%;">
                                <div class="avatar avatar-sm pull-up">
                                    <a href="{{ route('show-doctor', ['id' => $item->doctor_id]) }}">
                                        <img class="rounded-circle me-1 border" src="{{ asset('assets/doctors/' . $item->doctor->img) }}" onerror="this.src='https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT45gcJLX6J9Wlyr4rFHA3beqZJbTyCvo_0whWJegVnZQ&s'" alt="Avatar">
                                        {{ $item->doctor->name }}
                                    </a>
                                </div>
                            </td>
                         
                            <td>{{ $item->date }}</td>
                            <td>{{ $item->start_time }}</td>
                            <td>Rs:{{ $item->doctor->price }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!--/ DataTable with Buttons -->
      </div>
      <!-- / Content -->

      <!-- Footer -->
      @include('backend_app.layouts.footer')
      <div class="content-backdrop fade"></div>
    </div>
    <!-- Content wrapper -->
</div>

<!-- Flatpickr CSS and JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>



@endsection
