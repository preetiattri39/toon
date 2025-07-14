@extends('admin.layouts.master')
@section('title', 'Help And Support')
@section('content')
<div class="content-wrapper user-manage-box">
    <div class="top-titlebar pb-3">
        <h2 class="f-20 bold title-main">Help And Support</h2>
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
                            <option value="closed" class="border-bottom pb-3">Closed</option>
                            <option value="open">Open</option>
                            <option value="pending">Pending</option>
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
                       
                        <table id="tickets-table" class="table table-dark w-100">
                            <thead>
                                <tr>
                                    <th>Sr.No</th>
                                    <th>Sender</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Action</th>
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
<!-- Include jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#tickets-table').DataTable({
            processing: true,  // Show processing indicator
            serverSide: true,  // Use server-side processing
            ajax: {
                url: '{{ route('ticket-data') }}',  // Fetch data from the server
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
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'phone_number', name: 'phone_number' },
                { data: 'status', name: 'status' },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        let ticketDetailsUrl = '{{ route('ticket-details', ['ticket_id' => '__ticket_id__']) }}'.replace('__ticket_id__', row.id);

                        return `<div class="td-delete-icon d-flex gap-3">
                                   <a href="${ticketDetailsUrl}" class="px-1">
                                        <img src="{{ asset('assets/icons/td-eye.png') }}" alt="View">
                                    </a>
                                    <a href="javascript:void(0);" class="delete-btn px-1" data-ticket-id="${row.id}">
                                        <img src="{{asset('assets/icons/Delete Icon.png')}}" alt="Delete">
                                    </a>
                                </div>`;
                    }
                }
            ]
        });

        // Custom search input (for Name, Email, Phone)
        $('#search_by').on('keyup', function() {
            table.ajax.reload();
        });

        // Filter by Active/Inactive users
        $('#status_filter').on('change', function() {
            table.ajax.reload();
        });
    });
</script>


<!-- JavaScript to trigger SweetAlert and delete -->
<script>
    $(document).on('click', '.delete-btn', function() {
        var ticketId = $(this).data('ticket-id'); // Get ticket ID

        // SweetAlert confirmation popup
        Swal.fire({
            title: 'Are you sure?',
            text: 'You want to delete this ticket?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '{{ route("delete-ticket", ["ticket_id" => "__ticket_id__"]) }}'.replace("__ticket_id__", ticketId);
            }
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