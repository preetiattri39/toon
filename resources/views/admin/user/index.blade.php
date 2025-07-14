@extends('admin.layouts.master')
@section('title', 'Users')
@section('content')

<div class="content-wrapper user-manage-box">
    <div class="top-titlebar pb-3">
        <h2 class="f-20 bold title-main">Users</h2>
    </div>
    <div class="search-filter-box pl-3 py-2  my-2 pr-2 " bis_skin_checked="1">
        <div class="row align-items-center gy-lg-0 gy-3" bis_skin_checked="1">
            <div class="col-md-8 col-6" bis_skin_checked="1">
                <div class=" " bis_skin_checked="1">
                    <div class="search-container" bis_skin_checked="1">
                        <input type="text" placeholder="Search..." class="search-input light-gray fs-14" name="search_by" id="search_by">
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-6" bis_skin_checked="1">
                <div class="filters mr-3 d-flex gap-2 justify-content-lg-end justify-content-center " bis_skin_checked="1">
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
                       
                        <table id="users-table" class="table table-dark w-100">
                        <thead>
                            <tr>
                                <th> Sr. No </th>
                                <th> User's Name </th>
                                <th> Phone Number </th>
                                <th> Email </th>
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

<script type="text/javascript">
    
    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#users-table').DataTable({
            processing: true,  // Show processing indicator
            serverSide: true,  // Use server-side processing
            ajax: {
                url: '{{ route('user-data') }}',  // Fetch data from the server
                data: function(d) {
                    d.custom_search = $('#search_by').val();  // Send custom search value
                    d.status_filter = $('#status_filter').val(); // Send selected status filter value
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
                { data: 'name', name: 'name' },                  // User's Name
                { data: 'phone_number', name: 'phone_number' },  // Phone Number
                { data: 'email', name: 'email' },                // Email
                {
                    data: 'status', // Status column
                    name: 'status',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return `<label class="switch m-0 mx-3">
                                    <input type="checkbox" ${data == 1 ? 'checked' : ''} class="status_user" value="${row.id}">
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
                        let userDetailsUrl = '{{ route('user-details', ['user_id' => '__user_id__']) }}'.replace('__user_id__', row.id);

                        return `<div class="td-delete-icon d-flex gap-3">
                                <a href="${userDetailsUrl}" class="px-1">
                                        <img src="{{ asset('assets/icons/td-eye.png') }}" alt="View">
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

    $(document).on('change','input.status_user',function(){ 

        var user_id = $(this).val();

        $.ajax({
            method: 'GET',
            url: '{{route("user-status")}}',
            data: {
                _token: "{{ csrf_token() }}", 
                user_id:user_id
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