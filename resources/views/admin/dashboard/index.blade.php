@extends('admin.layouts.master')
@section('title', 'Dahsboard')
@section('content')
<div class="content-wrapper inner-dashboard">
<div class="row">
    <!-- <div class="col-sm-4 grid-margin">
        <div class="card">
            <div class="card-body">
                <h5>Revenue</h5>
                <div class="row">
                    <div class="col-8 col-sm-12 col-xl-8 my-auto">
                    <div class="d-flex d-sm-block d-md-flex align-items-center">
                        <h2 class="mb-0">$32123</h2>
                        <p class="text-success ml-2 mb-0 font-weight-medium">+3.5%</p>
                    </div>
                    <h6 class="text-muted font-weight-normal">11.38% Since last month</h6>
                    </div>
                    <div class="col-4 col-sm-12 col-xl-4 text-center text-xl-right">
                    <i class="icon-lg mdi mdi-codepen text-primary ml-auto"></i>
                    </div>
                </div>
            </div>
        </div>
    </div> -->
    <div class="col-sm-6 grid-margin">
        <div class="card">
            <div class="card-body">
            <h5>Sales</h5>
            <div class="row">
                <div class="col-8 col-sm-12 col-xl-8 my-auto">
                <div class="d-flex d-sm-block d-md-flex align-items-center">
                    <h2 class="mb-0">${{number_format($current_sales, 2)}}</h2>
                    <p class="text-success ml-2 mb-0 font-weight-medium">+{{number_format($growth_percentage, 2)}}%</p>
                </div>
                <h6 class="text-muted font-weight-normal"> {{number_format($percentage_since_last_month, 2)}}% Since last month</h6>
                </div>
                <div class="col-4 col-sm-12 col-xl-4 text-center text-xl-right">
                <i class="icon-lg mdi mdi-wallet-travel text-danger ml-auto"></i>
                </div>
            </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 grid-margin">
        <div class="card">
            <div class="card-body">
            <h5>Customers</h5>
            <div class="row">
                <div class="col-8 col-sm-12 col-xl-8 my-auto">
                <div class="d-flex d-sm-block d-md-flex align-items-center">
                    <h2 class="mb-0">{{$current_users}}</h2>
                    <p class="text-success ml-2 mb-0 font-weight-medium">{{$growth_percentage_user}}% </p>
                </div>
                <h6 class="text-muted font-weight-normal">{{number_format($percentage_since_last_month_user,2)}}% Since last month</h6>
                </div>
                <div class="col-4 col-sm-12 col-xl-4 text-center text-xl-right">
                <i class="icon-lg mdi mdi-monitor text-success ml-auto"></i>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>

<div class="row ">
    <div class="col-12 grid-margin">
    <div class="card">
        <h4 class="card-title px-3 py-3">Order Status</h4>
        <div class="card-body p-0">
        
        <div class="table-responsive">
            <table class="table table-dark shadow w-100" id="order-table">
                <thead>
                    <tr>
                        <th> Sr.No </th>
                        <th> Customer Name </th>
                        <th> Order No </th>
                        <th> Total Amount </th>
                        <th> Payment Mode </th>
                        <th> Date </th>
                        <th> Payment Status </th>
                    </tr>
                </thead>
                <tbody>
                    <!-- <tr>
                        <td>
                            <img src="{{asset('assets/images/faces/face1.jpg')}}" alt="image">
                            <span class="pl-2">Henry Klein</span>
                        </td>
                        <td> 02312 </td>
                        <td> $14,500 </td>
                        <td> Credit card </td>
                        <td> 04 Dec 2019 </td>
                        <td>
                            <div class="badge badge-outline-success">Approved</div>
                        </td>
                    </tr> -->
                </tbody>
            </table>
        </div>
        </div>
    </div>
    </div>
</div>
</div>
@endsection


@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#order-table').DataTable({
            processing: true,  
            serverSide: true,
            ajax: {
                url: '{{ route('order-data') }}', 
            },
            searching: false,  // Disable default search box
            lengthChange: false, 
            order: [[0, 'desc']],
            columns: [
                {
                    data: null,  // No actual data field for serial number
                    name: 'id',
                    render: function(data, type, row, meta) {
                        return meta.row + 1;  // Row index + 1 to start serial number from 1
                    }
                },
                {
                    data: 'null',
                    name: 'user_id',
                    render: function(data, type, row) {
                        if (row.user) {
                            var profilePic = row.user.profile_pic && row.user.profile_pic.trim() !== '' 
                                ? '{{ asset('storage/profile/') }}'+'/'+row.user.profile_pic 
                                : '{{ asset('assets/icons/user-default.jpg') }}'; 

                            return '<img src="' + profilePic + '" alt="image" class="rounded-circle" style="width: 40px; height: 40px;">' 
                                + '<span class="pl-2">' + row.user.name + '</span>';
                        }
                        return null;
                    }
                },
                { data: 'order_number', name: 'order_number' },
                { 
                    data: 'total', 
                    name: 'total',
                    render: function(data, type, row) {
                        if (data) {
                            // Format the total as currency (e.g., $100.00)
                            return '$' + parseFloat(data).toFixed(2); // Ensure two decimal places
                        }
                        return data; // Return raw if total is not available
                    }
                },
                { data: 'payment_method', name: 'payment_method' },
                { 
                    data: 'created_at', 
                    name: 'created_at',
                    render: function(data, type, row) {
                        // Check if the date is available
                        if (data) {
                            return moment(data).format('DD MMM YYYY'); // Format the date to '25 Mar 2025'
                        }
                        return data; // Return raw if date is not available
                    }
                },
                {
    data: 'payment_status',
    name: 'payment_status',
    render: function(data, type, row) {
        // Set class based on payment status
        var statusClass = '';
        switch (data.toLowerCase()) {
            case 'paid':
                statusClass = 'payment-status staus-success';
                break;
            case 'pending':
                statusClass = 'payment-status staus-warning';
                break;
            case 'failed':
            case 'cancelled':
                statusClass = 'payment-status staus-danger';
                break;
            default:
                statusClass = 'payment-status staus-warning';
        }

        return '<span class="' + statusClass + '">' + data + '</span>';
    }
}
            ]
        });
    });
</script>

<script>
    @if(session('success'))
        toastr.success("{{ session('success') }}");
    @endif
    @if(session('error'))
        toastr.error("{{ session('error') }}");
    @endif
</script>
@endsection