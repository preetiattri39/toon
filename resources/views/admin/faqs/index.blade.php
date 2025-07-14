@extends('admin.layouts.master')
@section('title', 'Faqs')
@section('content')

<div class="content-wrapper user-manage-box">
    <div class="top-titlebar pb-3">
        <h2 class="f-20 bold title-main">Faqs</h2>
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
                       
                        <table id="faqs-table" class="table table-dark w-100">
                        <thead>
                            <tr>
                                <th> Sr. No </th>
                                <th> Questions </th>
                                <th> Description </th>
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
        var table = $('#faqs-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('faq-data') }}',  // Fetch data from the server
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
                { data: 'question', name: 'question' },
                {
                    data: 'answer',
                    render: function(data, type, row, meta) {
                        var shortAnswer = data.length > 100 ? data.substring(0, 40) + '...' : data;
                        return '<span title="' + data + '">' + shortAnswer + '</span>';
                    }
                },
                {
                    data: 'is_active', // Status column
                    name: 'is_active',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return `<label class="switch m-0 mx-3">
                                    <input type="checkbox" ${data == 1 ? 'checked' : ''} class="faq_status" value="${row.id}">
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
                        let faqDetailsUrl = '{{ route('edit-faq', ['faq_id' => '__faq_id__']) }}'.replace('__faq_id__', row.id);

                        return `<div class="td-delete-icon d-flex gap-3">
                                <a href="${faqDetailsUrl}" class="px-1">
                                        <img src="{{ asset('assets/icons/td-eye.png') }}" alt="View">
                                    </a>
                                    <a href="javascript:void(0);" class="delete-btn px-1" data-faq-id="${row.id}">
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

    });

    $(document).on('change','input.faq_status',function(){ 

        var faq_id = $(this).val();

        $.ajax({
            method: 'GET',
            url: '{{route("update-faq-status")}}',
            data: {
                _token: "{{ csrf_token() }}", 
                faq_id:faq_id
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
    $(document).on('click', '.delete-btn', function() {
        var faq_id = $(this).data('faq-id'); // Get ticket ID

        // SweetAlert confirmation popup
        Swal.fire({
            title: 'Are you sure?',
            text: 'You want to delete this faq?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '{{ route("faq-delete", ["faq_id" => "__faq_id__"]) }}'.replace("__faq_id__", faq_id);
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