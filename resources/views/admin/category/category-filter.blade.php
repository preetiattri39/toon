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