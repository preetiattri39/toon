@extends('admin.layouts.master')
@section('title', 'Dahsboard')
@section('content')

<div class="content-wrapper user-manage-box">
    <div class="top-titlebar pb-3">
        <h2 class="f-20 bold title-main">Categories</h2>
    </div>
    <div class="search-filter-box pl-3 py-2 my-2 pr-2 " bis_skin_checked="1">
        <div class="row align-items-center gy-lg-0 gy-3" bis_skin_checked="1">
            <div class="col-md-8 col-6" bis_skin_checked="1">
                <div class=" " bis_skin_checked="1">
                    <div class="search-container" bis_skin_checked="1">
                        <input type="text" placeholder="Search..." class="search-input light-gray fs-14" name="search_by" id="search_by">
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-6" bis_skin_checked="1">
                <div class="filters d-flex gap-2 justify-content-lg-end justify-content-center " bis_skin_checked="1">
                    <div class="status-btn" bis_skin_checked="1">
                        <select id="status" class="form-control wm-content">
                            <option value="">Status</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <ul id="category-listing" class="p-0">
        @if($categories->count()>0)
            @foreach($categories as $category)
                <li>
                    <!-- If the category has no parent_id, it's a main category -->
                    @if(empty($category->parent_id))
                        <!-- <span class="toggle-icon">+</span> -->
                    @endif
                    <a href="#"  class="px-2">{{$category->categoryTranslations->name}}</a>
                    <div class="d-flex align-items-center right-side-btn">
                        <label class="switch m-0 mx-3">
                            <input type="checkbox" {{ $category->status == 1 ? 'checked' : '' }} id="cat_status" value="{{$category->id}}">
                            <span class="slider round"></span>
                        </label>
                        <div class="td-delete-icon d-flex gap-3" bis_skin_checked="1">

                            <a href="{{route('edit-category',['cat_id'=>$category->id])}}" class="px-1">
                                <img src="{{asset('assets/icons/td-eye.png')}}">
                            </a>                                                                                                                                                                                                                                                                                         
                            <a href="javascript:void(0);" class="delete-btn px-1" data-cat-id="{{$category->id}}">
                                <img src="{{asset('assets/icons/Delete Icon.png')}}" alt="Delete">
                            </a>
                        </div>
                </div>
        
                    <!-- Check if the category has subcategories -->
                    <!-- @if($category->subCategories->isNotEmpty())
                        <ul class="sub-category-list p-0">
                            @foreach($category->subCategories as $subcategory)
                                <li>
                                    <span class="toggle-icon">+</span>
                                    <a href="#" class="px-2">{{$subcategory->categoryTranslations->name}}</a>
                                    <div class="d-flex align-items-center child-right-side-btn">
                                        <label class="switch m-0 mx-3">
                                            <input type="checkbox" {{ $subcategory->status == 1 ? 'checked' : '' }} id="cat_status" value="{{$subcategory->id}}">
                                            <span class="slider round"></span>
                                        </label>
                                        <div class="td-delete-icon d-flex gap-3" bis_skin_checked="1">
                                           <a href="{{route('edit-category',['cat_id'=>$subcategory->id])}}" class="px-1">
                                                <img src="{{asset('assets/icons/td-eye.png')}}">
                                            </a>                                                                                                                                                                                                                                                                                         
                                            <a href="javascript:void(0);" class="delete-btn px-1" data-cat-id="{{$subcategory->id}}">
                                                <img src="{{asset('assets/icons/Delete Icon.png')}}" alt="Delete">
                                            </a>
                                        </div>
                                </div>

                                    @if($subcategory->subCategories->isNotEmpty())
                                        <ul class="sub-child-category p-0">
                                            @foreach($subcategory->subCategories as $thirdsubcategory)
                                                <li>
                                                    <a href="#">{{$thirdsubcategory->categoryTranslations->name}}</a>
                                                    <div class="d-flex align-items-center sub-child-right-side-btn">
                                                        <label class="switch m-0 mx-3">
                                                            <input type="checkbox" {{ $thirdsubcategory->status == 1 ? 'checked' : '' }} id="cat_status" value="{{$thirdsubcategory->id}}">
                                                            <span class="slider round"></span>
                                                        </label>
                                                        <div class="td-delete-icon d-flex gap-3" bis_skin_checked="1">
                                                           <a href="{{route('edit-category',['cat_id'=>$thirdsubcategory->id])}}" class="px-1">
                                                                <img src="{{asset('assets/icons/td-eye.png')}}">
                                                            </a>                                                                                                                                                                                                                                                                                         
                                                            <a href="javascript:void(0);" class="delete-btn px-1" data-cat-id="{{$thirdsubcategory->id}}">
                                                                <img src="{{asset('assets/icons/Delete Icon.png')}}" alt="Delete">
                                                            </a>
                                                        </div>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif -->

                  
                </li>
            @endforeach
        @else
            <li>Category Not Found</li>
        @endif
    </ul>
    <div class="total-drivers-box d-flex justify-content-end align-items-center mb-5 pb-5 mt-md-0 mt-3">
        @if ($categories->hasPages())
            <div class="custom-pagination">
                <div class="pagination-info">
                    {{ $categories->firstItem() }}–{{ $categories->lastItem() }} of {{ $categories->total() }} items
                </div>
                <ul class="pagination">
                    {{-- Previous Page Link --}}
                    @if ($categories->onFirstPage())
                        <li class="page-item disabled"><span>&lt;</span></li>
                    @else
                        <li class="page-item">
                            <a href="{{ $categories->previousPageUrl() }}" rel="prev">&lt;</a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($categories->getUrlRange(1, $categories->lastPage()) as $page => $url)
                        @if ($page == $categories->currentPage())
                            <li class="page-item active"><span>{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($categories->hasMorePages())
                        <li class="page-item">
                            <a href="{{ $categories->nextPageUrl() }}" rel="next">&gt;</a>
                        </li>
                    @else
                        <li class="page-item disabled"><span>&gt;</span></li>
                    @endif
                </ul>
            </div>
        @endif
    </div>
</div>
@endsection


@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- JavaScript to trigger SweetAlert and delete -->
<script>
    $(document).on('click', '.delete-btn', function() {
        var catId = $(this).data('cat-id');

        // SweetAlert confirmation popup
        Swal.fire({
            title: 'Are you sure?',
            text: 'You want to delete this category?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect to the delete route if confirmed
                window.location.href = '{{ route("delete-category", ["cat_id" => "__cat_id__"]) }}'.replace("__cat_id__", catId);
            }
        });
    });
</script>

<script>



    $(document).ready(function(){

        $("input#cat_status").change(function(){
            
            var category_id = $(this).val();

            $.ajax({
                method: 'GET',
                url: '{{route("category-status")}}',
                data: {
                    _token: "{{ csrf_token() }}", 
                    category_id:category_id
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

        $("#search_by").keyup(function(){
            var search_by = $(this).val();
            categoryFilter(search_by,null);
        })

        $("select#status").change(function(){
            var status = $(this).val();
            categoryFilter(null, status);
        })

        function categoryFilter(search_by, status){
            $.ajax({
                method: 'POST',
                url: '{{route("category-filter")}}',
                data: {
                    _token: "{{ csrf_token() }}", 
                    search_by: search_by,
                    status: status
                },
                success: function (response) {
                    // console.log("Response received:", response);
                    $('#category-listing').html(response);
                },
                error: function (xhr, status, error) {
                    console.error('An error occurred:', error); // Log the error for debugging
                }
            });
        }

    });
</script>

<script>
   document.addEventListener('click', function(event) {
        if (event.target.classList.contains('toggle-icon')) {
            const parentLi = event.target.parentElement;
            const subcategories = parentLi.querySelector('ul');

            if (subcategories) {
                // Toggle visibility of the subcategories
                if (subcategories.style.display === 'none' || subcategories.style.display === '') {
                    subcategories.style.display = 'block';
                    event.target.textContent = '-'; // Change icon to minus when open
                } else {
                    subcategories.style.display = 'none';
                    event.target.textContent = '+'; // Change icon to plus when closed
                }
            }
        }
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