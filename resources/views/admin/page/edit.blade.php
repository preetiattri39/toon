@extends('admin.layouts.master')
@section('title', 'Pages')
@section('content')
<div class="content-wrapper user-manage-box">
    <div class="top-titlebar pb-2">
        <h2 class="f-20 bold title-main">Content Management</h2>
    </div>
    <div class="content-page-box">
        <div class="card">
            <div class="card-body">
                <form action="{{route('update-page-content')}}" method="POST" id="update-content">
                    @csrf
                    <input type="hidden" name="page_id" id="page_id" value="{{$page->id}}">
                    <div class="content-page-form">
                        <div class="col-md-12 p-0">
                            <div class="c-title-input mb-3">
                                <label for="content_title" class="form-label fw-600">Title<span class="text-danger">*</span></label>
                                <input type="text" class="form-control rounded-30 bg-f6 @error('content_title') is-invalid @enderror" id="content_title" name="content_title" placeholder="title" value="{{$page->PageContent->name}}">
                            </div>
                            @error('content_title')
                                <div class="invalid-feedback">{{$message}}</div>
                            @enderror
                        </div>
                        <div class="col-md-12 p-0">
                            <div class="c-title-input">
                                <label for="page_slug" class="form-label fw-600">Slug</label>
                                <input type="text" class="form-control rounded-30 bg-f6 @error('page_slug') is-invalid @enderror" id="page_slug" name="page_slug" value="{{$page->slug}}" disabled>
                            </div>
                        </div>
                        @error('page_slug')
                            <div class="invalid-feedback">{{$message}}</div>
                        @enderror
                        <div class="text-editor-box">
                            <h6 class="color-23 fw-600 mt-3">Content</h6>
                            <div id="wysiwyg">
                                <textarea id="content_editor" name="content" class="form-control rounded-30 bg-f6">
                                {!! $page->PageContent->page_content !!}
                                </textarea>
                            </div>
                            <div class="submit-btn mt-4">
                                <button type="submit" class="btn btn-primary mr-2" id="submitButton">Update</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.tiny.cloud/1/k6vov7xs3x5yy8qq6m6nl4qolwen4gg1kedvjbqk7cae33hv/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

<!-- Place the following <script> and <textarea> tags your HTML's <body> -->
<script>
  tinymce.init({
    selector: 'textarea',
    plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
  });
</script>

<script>
function disableButton() {
    var submitButton = document.getElementById('submitButton');
    submitButton.disabled = true;
    submitButton.innerHTML = 'Submitting...';
}
</script>

<script>
    $("#update-content").validate({
        rules: {
            content_title: {
                required: true,
            },
        },
        messages: {
            content_title: {
                required: "Title is required.",
            },
        },
        submitHandler: function(form) {
            disableButton();
            form.submit();
        }
    });

    @if(session('success'))
        toastr.success("{{ session('success') }}");
    @endif
    @if(session('error'))
        toastr.error("{{ session('error') }}");
    @endif
</script>
@endsection
