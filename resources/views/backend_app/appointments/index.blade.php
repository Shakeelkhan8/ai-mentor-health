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
        <h4 class="py-3 mb-4"><span class="text-dark fw-dark">Doctors List</h4>

        <!-- DataTable with Buttons -->
        <div class="card">
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                        <tr class="text-nowrap">
                            <th>Id</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone no</th>
                            <th>Designation</th>
                            <th>Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="table-body">
                    @foreach ($doctors as $key => $doctor)
                        <tr>
                            <td>{{ $doctor->id }}</td>
                            <td style="width:20%;">
                                <div class="avatar avatar-sm pull-up">
                                    <a href="{{ route('show-doctor', ['id' => $doctor->id]) }}">
                                        <img class="rounded-circle me-1 border" src="{{ asset('assets/doctors/' . $doctor->img) }}" onerror="this.src='https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT45gcJLX6J9Wlyr4rFHA3beqZJbTyCvo_0whWJegVnZQ&s'" alt="Avatar">
                                        {{ $doctor->name }}
                                    </a>
                                </div>
                            </td>
                            <td>{{ $doctor->email }}</td>
                            <td>{{ $doctor->phone_no }}</td>
                            <td>{{ $doctor->designation }}</td>
                            <td>{{ $doctor->price }}</td>
                            <td>
                                <button class="btn btn-primary" style="font-size: 12px;" data-bs-toggle="modal" data-bs-target="#appointmentModal_{{ $key }}">Make Appointment</button>
                            </td>
        
                            <!-- Appointment Modal -->
                            <div class="modal fade" id="appointmentModal_{{ $key }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content p-3">
                                        <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
                                        <div class="modal-body">
                                            <div class="text-center mb-4">
                                                <h3 class="mb-2">Book an Appointment</h3>
                                                <p class="text-muted">Set an appointment with {{ $doctor->name }}</p>
                                            </div>
                                            <form action="{{ route('appointment.store')}}" method="post">
                                                @csrf
                                                <div class="mb-3">
                                                    <label for="appointment_date">Appointment Date & Time</label>
                                                    <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">
                                                    <input type="hidden" name="user_id" value="{{ $auth_id }}">
                                                    <input type="text" id="appointment_date_{{ $key }}" name="appointment_date" class="form-control datepicker" placeholder="Select Date and Time" />
                                                </div>
                                                <div class="mb-3">
                                                    <button type="submit" class="btn btn-primary">Confirm Appointment</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
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

<script>
    document.addEventListener("DOMContentLoaded", function() {
        @foreach ($doctors as $key => $doctor)
            (function() {
                const doctorAppointments = @json($doctor->appointments);
                // Initialize Flatpickr for Date and Time for each doctor
                flatpickr("#appointment_date_{{ $key }}", {
                    enableTime: true,
                    dateFormat: "Y-m-d H:i",
                    time_24hr: false,
                    minDate: "today",
                    minTime: "08:00",
                    maxTime: "18:00",
                    disable: doctorAppointments.map(appointment => {
                        let start = new Date(`1970-01-01T${appointment.start_time}`);
                        let end = new Date(start.getTime() + 30 * 60000); // 30 minutes buffer
                        return {
                            from: start.toTimeString().slice(0, 5),
                            to: end.toTimeString().slice(0, 5)
                        };
                    })
                });
            })(); // IIFE to create a unique scope per doctor
        @endforeach
    });
</script>


@endsection
