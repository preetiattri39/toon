@extends('admin.layouts.master')
@section('title', 'Products')
@section('content')

<div class="content-wrapper user-manage-box">
    <div class="top-titlebar pb-md-3 pb-1">
        <h2 class="f-20 bold title-main">Products</h2>
    </div>
    <div class="search-filter-box px-3 py-md-2 py-3 my-2 pr-2 " bis_skin_checked="1">
        <div class="row align-items-center gy-lg-0 gy-3 " bis_skin_checked="1">
            <div class="col-md-6 col-sm-6 col-12" bis_skin_checked="1">
                <div class=" " bis_skin_checked="1">
                    <div class="search-container" bis_skin_checked="1">
                        <input type="text" placeholder="Search..." class="search-input light-gray fs-14" name="search_by" id="search_by">
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-6" bis_skin_checked="1">
                <div class="filters mr-3 d-flex gap-2 justify-content-lg-end justify-content-center " bis_skin_checked="1">
                    <div class="status-btn" bis_skin_checked="1">
                        <select id="publish_status_filter" class="form-control wm-content">
                            <option value="">Publish Status</option>
                            <option value="published">Published</option>
                            <option value="draft">Draft</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-6" bis_skin_checked="1">
                <div class="filters d-flex gap-2 justify-content-lg-end justify-content-end " bis_skin_checked="1">
                    <div class="status-btn" bis_skin_checked="1">
                        <select id="status_filter" class="form-control wm-content">
                            <option value="">Status</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
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
                       
                        <table id="product-table" class="table table-dark w-100">
                        <thead>
                            <tr>
                                <th> Sr. No </th>
                                <th> Name </th>
                                <th> Quantity </th>
                                <th> Regular Price </th>
                                <th> Discounted Price </th>
                                <th> Category </th>
                                <th> Publish Status </th>
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
        var table = $('#product-table').DataTable({
            processing: true,  // Show processing indicator
            serverSide: true,  // Use server-side processing
            ajax: {
                url: '{{ route('product-data') }}',  // Fetch data from the server
                data: function(d) {
                    d.custom_search = $('#search_by').val();  // Send custom search value
                    d.status_filter = $('#status_filter').val(); // Send selected status filter value
                    d.publish_status_filter = $('#publish_status_filter').val(); // Send selected status filter value
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
                    data: 'name',
                    name: 'name',
                    render: function(data, type, row) {
                    let imageUrl = row.cover_image ? row.cover_image : 'face1.jpg'; // Provide a default image if none exists
                    let productName = row.product_translation ? row.product_translation.name : '';

                    return `
                        <div style="display: flex; align-items: center;">
                            <img src="{{ asset('storage/product/') }}/${imageUrl}" alt="Product Image" style="width: 34px; height: 34px; object-fit: cover; margin-right: 10px; border-radius: 20px;">
                            <span>${productName}</span>
                        </div>
                    `;
                    }
                },
                { data: 'stock_quantity', name: 'stock_quantity' },
                { data: 'regular_price', name: 'regular_price' },
                { data: 'discounted_price', name: 'discounted_price' },
                {
                    data: 'Category',
                    name: 'Category',
                    render: function(data, type, row) {
                        return row.category.category_translations.name ? row.category.category_translations.name : '';
                    }
                },
                { data: 'publish', name: 'publish' },
                {
                    data: 'status', // Status column
                    name: 'status',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return `<label class="switch m-0 mx-3">
                                    <input type="checkbox" ${data == 1 ? 'checked' : ''} class="product_status" value="${row.id}">
                                    <span class="slider round"></span>
                                </label>`;
                    }
                },
                {
                    data: 'action', // Action column
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        let productDetailsUrl = '{{ route('edit-product', ['product_id' => '__product_id__']) }}'.replace('__product_id__', row.id);

                        return `<div class="td-delete-icon d-flex gap-3">
                                    <a href="${productDetailsUrl}" class="px-1">
                                        <img src="{{ asset('assets/icons/td-eye.png') }}" alt="View">
                                    </a>                                                                                                                                                                                                                                                                                 
                                    <a href="javascript:void(0);" class="delete-btn px-1" data-product-id="${row.id}">
                                        <img src="{{asset('assets/icons/Delete Icon.png')}}" alt="Delete">
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

        // Filter by Active/Inactive users
        $('#publish_status_filter').on('change', function() {
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