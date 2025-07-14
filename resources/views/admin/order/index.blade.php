@extends('admin.layouts.master')
@section('title', 'Orders')
@section('content')

<div class="content-wrapper user-manage-box">
    <div class="top-titlebar pb-3">
        <h2 class="f-20 bold title-main">Orders</h2>
    </div>
    <div class="search-filter-box px-3 py-md-2 py-3 my-2 pr-2 " bis_skin_checked="1">
        <div class="row align-items-center gy-lg-0 gy-3 " bis_skin_checked="1">
            <div class="col-md-8 col-6" bis_skin_checked="1">
                <div class=" " bis_skin_checked="1">
                    <div class="search-container" bis_skin_checked="1">
                        <input type="text" placeholder="Search..." class="search-input light-gray fs-14" name="search_by" id="search_by">
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-6" bis_skin_checked="1">
                <div class="filters d-flex gap-2 justify-content-lg-end justify-content-end " bis_skin_checked="1">
                    <div class="status-btn" bis_skin_checked="1">
                        <select id="status_filter" class="form-control wm-content">
                            <option value="">Status</option>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="shipped">Shipped</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                       
                        <table id="order-table" class="table table-dark w-100">
                        <thead>
                            <tr>
                                <th> Sr. No </th>
                                <th> Customer Name </th>
                                <th> Order No </th>
                                <th> Date </th>
                                <th> Amount </th>
                                <th> Status </th>
                                <th> Action </th>
                            </tr>
                        </thead>
                            <tbody>
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
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>


<!-- JavaScript to trigger SweetAlert and delete -->
<script>
     $(document).on('click', '.delete-btn', function() {
        var productId = $(this).data('product-id');

        // SweetAlert confirmation popup
        Swal.fire({
            title: 'Are you sure?',
            text: 'You want to delete this product?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect to the delete route if confirmed
                window.location.href = '{{ route("delete-product", ["product_id" => "__product_id__"]) }}'.replace("__product_id__", productId);
            }
        });
    });
</script>

<script type="text/javascript">
    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#order-table').DataTable({
            processing: true,  
            serverSide: true,
            ajax: {
                url: '{{ route('order-data') }}', 
                data: function(d) {
                    d.custom_search = $('#search_by').val(); 
                    d.status_filter = $('#status_filter').val();
                }
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
                { data: 'status', name: 'status' },
                {
                    data: 'action', // Action column
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        let viewOrderDetailsUrl = '{{ route('view-order', ['order_id' => '__order_id__']) }}'.replace('__order_id__', row.id);
                        let generateOrderPdfUrl = '{{ route('generate-order-pdf', ['order_id' => '__order_id__']) }}'.replace('__order_id__', row.id);

                        return `<div class="td-delete-icon d-flex gap-3">
                                    <a href="${viewOrderDetailsUrl}" class="px-1">
                                        <img src="{{ asset('assets/icons/td-eye.png') }}" alt="View">
                                    </a>    
                                     <a href="${generateOrderPdfUrl}" class="px-1">
                                        <img src="{{ asset('assets/icons/download.png') }}" alt="View">
                                    </a>       
                                </div>`;
                    }
                }
            ]
        });

        // Custom search input (for Name, Email, Phone)
        $('#search_by').on('keyup', function() {
            table.ajax.reload();  // Reload the table with the custom search term
        });

        // Filter by Active/Inactive users
        $('#status_filter').on('change', function() {
            table.ajax.reload();  // Reload the table when status filter changes
        });      

    });

    $(document).on('change','input.product_status',function(){     

        var product_id = $(this).val();

        $.ajax({
            method: 'GET',
            url: '{{route("product-status")}}',
            data: {
                _token: "{{ csrf_token() }}", 
                product_id:product_id
            },
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr, status, error) {
                console.error('An error occurred:', error); // Log the error for debugging
            }
        });
    })

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