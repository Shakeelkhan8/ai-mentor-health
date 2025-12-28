@extends('backend_app.layouts.template')
@section('content')
@php
    $user=Auth::user();
@endphp
<div class="layout-page">
    <!-- Navbar -->

    @include('backend_app.layouts.nav')
    <!-- / Navbar -->
    <!-- Content wrapper -->
    <div class="content-wrapper">
      <div class="container-xxl flex-grow-1 container-p-y">
<div class="chat-container card p-5">
        <h2>Nearest Hospitals</h2>
        <table class="table">
            <tr>
                <thead>
                    <th>Name</th>
                <th>Actions</th>
            </thead>
        </tr>

           <!-- Hospital List with Individual Modals -->
<tbody>
    @foreach ($hospitals as $index => $item)
    <tr class="mt-3 border-bottom border-white py-3">
        <td>
           <div class="d-flex flex-row gap-3">
            <div>
                <img class="bg-white rounded-circle shadow" src="https://cdn3d.iconscout.com/3d/premium/thumb/hospital-6101753-5023487.png" width="50px" height="50px" style="object-fit:cover;" alt="">
            </div>
            <div>
                <h6 class="mb-0">{{ $item['name'] }}</h6>
                <small class="d-block text-warning">{{ $item['full_address'] }}</small>
                <small class="text-success">{{ $item['phone_number'] }}</small>
            </div>
           </div>
        </td>
        <td>
           <!-- Trigger for the Modal -->
           <button class="btn btn-outline-primary" style="font-size: 12px;" data-bs-toggle="modal" data-bs-target="#hospitalModal{{ $index }}">
               View Detail
           </button>
        </td>
    </tr>

    <!-- Modal for Each Hospital -->
    <div class="modal fade" id="hospitalModal{{ $index }}" tabindex="-1" aria-labelledby="hospitalModalLabel{{ $index }}" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="hospitalModalLabel{{ $index }}">{{ $item['name'] }} - Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Full Address:</strong> {{ $item['full_address'] }}</p>
                    <p><strong>Phone Number:</strong> {{ $item['phone_number'] }}</p>
                    <p><strong>Review Count:</strong> {{ $item['review_count'] }}</p>
                    <p><strong>Rating:</strong> {{ $item['rating'] }}</p>
                    <p><strong>Timezone:</strong> {{ $item['timezone'] }}</p>
                    <p><strong>Website:</strong> <a href="http://{{ $item['website'] }}" target="_blank">{{ $item['website'] }}</a></p>
                    <p><strong>Place Link:</strong> <a href="{{ $item['place_link'] }}" target="_blank">View on Google Maps</a></p>
                    <p><strong>State:</strong> {{ $item['state'] }}</p>

                    <!-- Display Working Hours -->
                    <p><strong>Working Hours:</strong></p>
                    <ul>
                        @foreach ($item['working_hours'] as $day => $hours)
                            <li>{{ $day }}: {{ implode(', ', $hours) }}</li>
                        @endforeach
                    </ul>

             
                </div>
            </div>
        </div>
    </div>
    @endforeach
</tbody>


        </table>
</div>


@include('backend_app.layouts.footer')
      <!-- / Footer -->

<div class="content-backdrop fade"></div>
    </div>
    <!-- Content wrapper -->
  </div>
<script>
   if ("geolocation" in navigator) {
  navigator.geolocation.getCurrentPosition(function(position)  {
    console.log("Latitude: " + position.coords.latitude + "\nLongitude: " + position.coords.longitude);
    document.getElementById('lat').value = position.coords.latitude;
    document.getElementById('long').value = position.coords.longitude;
  });
} else {
  console.log("Geolocation is not supported by this browser.");
}

</script>
@endsection
