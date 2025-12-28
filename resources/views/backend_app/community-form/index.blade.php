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
                         
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone no</th>
                            <th>Message</th>

                        </tr>
                    </thead>
                    <tbody id="table-body">
                    @foreach ($forms as $key => $item)
                        <tr>
                        
                            <td style="width:20%;">
                                <div class="avatar avatar-sm pull-up">
                                 {{ $item->name }}
                                </div>
                            </td>
                            <td>{{ $item->email }}</td>
                            <td>{{ $item->phone }}</td>
                            <td>{{ $item->message }}</td>
                           
                          
        
                            
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


@endsection