@extends('admin.layouts.master')
@section('title', 'Edit category')
@section('content')
<div class="content-wrapper user-manage-box">
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Update Category</h4>
                    <form id="add-category" method="POST" action="{{route('update-category')}}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" class="form-control" name="category_id" id="category_id" value="{{$edit_category->id}}">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" id="name" value="{{$edit_category->categoryTranslations->name}}">
                            @error('name')
                                <div class="invalid-feedback">{{$message}}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            @if($edit_category->thumbnail)
                            <div class="mb-3">
                                <img src="{{asset('storage/category')}}/{{$edit_category->thumbnail}}" height=80 width=80>
                            </div>
                            @endif
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
    submitButton.disabled = true;
    submitButton.innerHTML = 'Submitting...';
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