@extends('admin.layouts.master')
@section('title', 'Pages')
@section('content')
<div class="content-wrapper user-manage-box">
    <div class="top-titlebar pb-2">
        <h2 class="f-20 bold title-main">Content Management</h2>
    </div>
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive" style="overflow-x: auto;">
                <table class="table table-dark shadow">
                    <thead class="bg-e6 rounded-8">
                        <tr>
                            <th>Title</th>
                            <th></th>
                            <th>Slug</th>
                            <th></th>
                            <th></th>

                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($pages->count()>0)
                            @foreach($pages as $page)
                                <tr>
                                    <td>
                                        {{$page->PageContent->name}}
                                    </td>
                                    <td></td>
                                    <td>
                                    {{$page->slug}}
                                    </td>
                                    <td></td>
                                    <td></td>

                                    <td>
                                        <div class="td-delete-icon d-flex gap-3">
                                            <a href="{{route('viewPage',['page_id'=>$page->id])}}" class="px-1">
                                                <img src="{{ asset('assets/icons/td-eye.png') }}" alt="View">
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                        <tr>
                            <td colspan="7" class="text-center">No Page found</td>
                        </tr>
                        @endif
                    
                    </tbody>
                </table>
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

<script>
@if(session('success'))
        toastr.success("{{ session('success') }}");
    @endif
    @if(session('error'))
        toastr.error("{{ session('error') }}");
    @endif
</script>

@endsection
