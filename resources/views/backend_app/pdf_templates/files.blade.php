

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  </head>
  <style>
   table {
    width: 100%;
    border-collapse: collapse;
    border-spacing: 0;
    border: 1px solid #ddd;
}

/* CSS for Table Header */
th {
    background-color: #f2f2f2;
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
}

/* CSS for Table Cells */
td {
    border: 1px solid #ddd;
    padding: 8px;
}

/* CSS for Alternate Row Colors */
tr:nth-child(even) {
    background-color: #f2f2f2;
}

/* CSS for Hover Effect */
tr:hover {
    background-color: #ddd;
}
  </style>
  <body>
    <h1 ><img src="{{asset('assets/users/hillview-png-1.png')}}" class="w-50" alt=""></h1>
    <h2 style="text-align: center">All Files</h2>
    <table class="table" style="font-size: 5px;">
        <thead>
            <tr class="text-nowrap">

                <th>Tracking no</th>
                <th>App Form No </th>
                <th>Id No</th>
                <th>Security No</th>
                <th>File Status</th>
                <th>Type</th>
                <th>Size</th>
                <th>Unit</th>
                <th>Total Amount</th>
                <th>Paid Amount</th>
                <th>Balance Amount</th>
                <th>File Location</th>
                <th>Assigned To</th>
                <th>Client Name</th>
                <th>Client Cnic</th>
                <th>Plot no</th>

            </tr>
        </thead>
        <tbody>
            @foreach ($full_data as $key => $element)
            <tr>

                <td><a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="View Detail" href="/file-data/{{$element['id']}}">{{$element['tracking']}}</a></td>
                <td>{{$element['app_form_no']}}</td>
                <td>{{$element['id_no']}}</td>
                <td>{{$element['security_no']}}</td>
                <td>
                    @if ($element['file_status']==="open")
                   <span class="badge bg-success"> {{$element['file_status']}}</span>
                   @elseif($element['file_status']=="blocked")
                   <span class="badge bg-danger"> {{$element['file_status']}}</span>
                   @elseif($element['file_status']=="closed")
                   <span class="badge bg-danger"> {{$element['file_status']}}</span>
                   @elseif($element['file_status']=="processing")
                   <span class="badge bg-warning"> {{$element['file_status']}}</span>
                   @elseif($element['file_status']   =="reserved")
                   <span class="badge bg-info"> {{$element['file_status']}}</span>
                   @elseif($element['file_status']   =="ready")
                   <span class="badge bg-primary"> {{$element['file_status']}}</span>
                   @elseif($element['file_status']   =="delivered")
                   <span class="badge bg-success"> {{$element['file_status']}}</span>

                    @endif
                    </td>
                <td>{{$element['type']}}</td>
                <td>{{$element['size']}}</td>
                <td>{{$element['unit']}}</td>
                <td class="text-primary">Rs: {{number_format($element['total_amount'], 2, '.', ',')}}</td>
                <td class="text-warning">Rs: {{number_format($element['paid_amount'], 2, '.', ',')}}</td>
                <td class="text-danger">Rs: {{number_format($element['balance_amount'], 2, '.', ',')}}</td>
                <td>{{$element['file_location']}}</td>
                <td><span>{{$element['dealer_name']}}</span><span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Update Dealer"></span>
                    <!-- Modal code for updating dealer -->
                </td>
                <td>{{$element['client_name']}}</td>
                <td>{{$element['client_cnic']}}</td>
                <td>{{$element['plot_no']}}</td>
            </tr>
            @endforeach



        </tbody>
    </table>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>
