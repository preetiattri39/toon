@extends('admin.layouts.master')
@section('title', 'Add category')
@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Create New Category</h4>
                    <form id="add-category" method="post" action="{{route('add-category')}}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" id="name" value="{{ old('name') }}">
                            @error('name')
                                <div class="invalid-feedback">{{$message}}</div>
                            @enderror
                        </div>
                        <!-- <div class="form-group">
                            <label for="parent_category">Parent Category</label>
                            <select class="form-control" id="parent_category" name="parent_category">
                                <option value="">--Select--</option>
                                @if(!empty($categories))
                                    @foreach($categories as $category)
                                        <option value="{{$category->id}}">{{$category->categoryTranslations->name}}</option>
                                        @if($category->subCategories->isNotEmpty())
                                            @foreach($category->subCategories as $subcategory)
                                                <option value="{{$subcategory->id}}">&nbsp;&nbsp;➝ {{$subcategory->categoryTranslations->name}}</option>
                                                @if($subcategory->subCategories->isNotEmpty())
                                                    @foreach($subcategory->subCategories as $thirdsubcategory)
                                                        <option value="{{$thirdsubcategory->id}}">&nbsp;&nbsp;&nbsp;&nbsp;➝ {{$thirdsubcategory->categoryTranslations->name}}</option>
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        </div> -->
                        <div class="form-group thumb-img">
                            <label>Thumbnail</label>
                            <input type="file" name="thumbnail" id="thumbnail" accept="image/*" class="file-upload-default">
                            <div class="input-group col-xs-12">
                                <input type="text" name="thumbnail" id="thumbnail" class="form-control file-upload-info" disabled="" placeholder="Upload Image">
                                <span class="input-group-append">
                                <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                                </span>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mr-2" id="submitButton">Submit</button>
                    </form>
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


<script>
function disableButton() {
    var submitButton = document.getElementById('submitButton');
    submitButton.disabled = true; // Disable the submit button to prevent multiple clicks
    submitButton.innerHTML = 'Submitting...'; // Change the button text to indicate the form is being submitted
}
</script>

<script>
$("#add-category").validate({
    rules: {
        name: {
            required: true,
        }
    },
    messages: {
        name: {
            required: "Name is required.",
        }
    },
    submitHandler: function(form) {
        disableButton();
        form.submit();
    }
});
</script>

<script>
    (function($) {
        'use strict';
        $(function() {
            $('.file-upload-browse').on('click', function() {
            var file = $(this).parent().parent().parent().find('.file-upload-default');
            file.trigger('click');
            });
            $('.file-upload-default').on('change', function() {
            $(this).parent().find('.form-control').val($(this).val().replace(/C:\\fakepath\\/i, ''));
            });
        });
    })(jQuery);

    @if(session('success'))
        toastr.success("{{ session('success') }}");
    @endif

    @if(session('error'))
        toastr.error("{{ session('error') }}");
    @endif

</script>
@endsection