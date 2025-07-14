@extends('admin.layouts.master')
@section('title', 'Add Product')
@section('content')
<div class="content-wrapper user-manage-box">
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Update Product</h4>
                    <form id="update-product" method="POST" action="{{route('update-product')}}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="product_id" value="{{$product->id ?? null}}">
                        <div class="form-group">
                            <label for="name">Product Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" id="name" value="{{$product->productTranslation->name ?? null}}">
                            @error('name')
                                <div class="invalid-feedback">{{$message}}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="stock_quantity">Quantity</label>
                            <input type="text" class="form-control @error('stock_quantity') is-invalid @enderror" name="stock_quantity" id="stock_quantity" value="{{$product->stock_quantity ?? null}}">
                            @error('stock_quantity')
                                <div class="invalid-feedback">{{$message}}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="regular_price">Regular Price</label>
                            <input type="text" class="form-control @error('regular_price') is-invalid @enderror" name="regular_price" id="regular_price" value="{{$product->regular_price ?? null}}">
                            @error('regular_price')
                                <div class="invalid-feedback">{{$message}}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="discounted_price">Discounted Price</label>
                            <input type="text" class="form-control" name="discounted_price" id="discounted_price" value="{{$product->discounted_price ?? null}}">
                        </div>
                        <div class="form-group">
                            <label for="publish_status">Publish Status</label>
                            <select class="form-control" id="publish_status" name="publish_status">
                                <option value="">--Select--</option>
                                <option value="published" {{ $product->publish == 'published' ? 'selected' : '' }}>Published</option>
                                <option value="draft" {{ $product->publish == 'draft' ? 'selected' : '' }}>Draft</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="category">Categories</label>
                            <select class="form-control" id="category" name="category">
                                <option value="">--Select--</option>
                                @if(!empty($categories))
                                    @foreach($categories as $category)
                                        <option value="{{$category->id}}" {{ $product->category_id == $category->id ? 'selected' : '' }}>{{$category->categoryTranslations->name}}</option>
                                        @if($category->subCategories->isNotEmpty())
                                            @foreach($category->subCategories as $subcategory)
                                                <option value="{{$subcategory->id}}" {{ $product->category_id == $subcategory->id ? 'selected' : '' }}>&nbsp;&nbsp;➝ {{$subcategory->categoryTranslations->name}}</option>
                                                @if($subcategory->subCategories->isNotEmpty())
                                                    @foreach($subcategory->subCategories as $thirdsubcategory)
                                                        <option value="{{$thirdsubcategory->id}}" {{ $product->category_id == $thirdsubcategory->id ? 'selected' : '' }}>&nbsp;&nbsp;&nbsp;&nbsp;➝ {{$thirdsubcategory->categoryTranslations->name}}</option>
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Cover Image</label>
                            <input type="file" name="cover_image" id="cover_image" accept="image/*,video/*" class="file-upload-default">
                            <div class="input-group col-xs-12">
                                <input type="text" name="cover_image" id="cover_image" class="form-control file-upload-info cover-img" disabled="" placeholder="Upload Image">
                                <span class="input-group-append">
                                <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                                </span>
                            </div>
                        </div>
                        <div class="preview-container" id="coverPreviewContainer"></div>
                        <div class="form-group">
                            <label>Media</label>
                            <input type="file" name="media[]" id="media" accept="image/*,video/*" class="multiple-file-upload-default" multiple>
                            <div class="input-group col-xs-12">
                                <input type="text" class="form-control file-upload-info multi-product-img" disabled="" placeholder="Upload Image">
                                <span class="input-group-append">
                                    <button class="file-upload-browse-multiple btn btn-primary" type="button">Upload</button>
                                </span>
                            </div>
                        </div>
                        <div class="preview-container" id="mediaPreviewContainer"></div>
                        <div class="form-group">
                            <label for="message">Product Message</label>                         
                            <input type="text" id="message" name="message" class="form-control rounded-30 bg-f6" value="{{$product->productTranslation->message}}">
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <div id="wysiwyg">
                                <textarea id="description" name="description" class="form-control rounded-30 bg-f6">
                                {!! $product->productTranslation->description !!}
                                </textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="short_description">Short Description</label>
                            <textarea id="short_description" name="short_description" class="form-control rounded-30 bg-f6">
                                {!! $product->productTranslation->short_description !!}
                            </textarea>
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
<script src="https://cdn.tiny.cloud/1/k6vov7xs3x5yy8qq6m6nl4qolwen4gg1kedvjbqk7cae33hv/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
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
  tinymce.init({
    selector: '#description',
    plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
  });
</script>

<script>

$(document).ready(function(){
    var trimmedDescription = $("#short_description").val().trim();
    $("#short_description").val(trimmedDescription);
});


$.validator.addMethod("validDiscount", function(value, element) {
    var regularPrice = parseFloat($("#regular_price").val());
    var discountedPrice = parseFloat(value);
    
    // If discounted_price is filled, it must not be greater than regular_price
    return this.optional(element) || (discountedPrice <= regularPrice);
}, "Discounted Price must be less than or equal to Regular Price.");

$("#update-product").validate({
    rules: {
        name: {
            required: true,
        },
        category: {
            required: true,
        },
        stock_quantity: {
            required: true,
            digits: true,
            min: 1, 
        },
        regular_price: {
            required: true,
            number: true,
            min: 0.01,
        },
        discounted_price: {
            number: true, 
            min: 0.01,
            validDiscount: true
        }
    },
    messages: {
        name: {
            required: "Name is required.",
        },
        category: {
            required: "Category is required.",
        },
        stock_quantity: {
            required: "Quantity is required.",
            digits: "Only whole numbers are allowed.",
            min: "Quantity must be at least 1."
        },
        regular_price: {
            required: "Regular Price is required.",
            number: "Please enter a valid price.",
            min: "Regular Price must be greater than 0."
        },
        discounted_price: {
            number: "Please enter a valid discounted price.",
            min: "Discounted Price must be greater than 0.",
            validDiscount: "Discounted Price must be less than or equal to Regular Price."
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

    (function($) {
    'use strict';
        $(function() {
            // Trigger file input click when the browse button is clicked
            $('.file-upload-browse-multiple').on('click', function() {
                var file = $(this).parent().parent().parent().find('.multiple-file-upload-default');
                file.trigger('click');
            });

            // Handle file selection
            $('.multiple-file-upload-default').on('change', function() {
                var fileNames = [];
                // Iterate over the selected files and add their names to the array
                $.each(this.files, function(index, file) {
                    fileNames.push(file.name);
                });
                // Join the file names and display them
                $(this).parent().find('.form-control').val(fileNames.join(', '));
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

<script>
    document.addEventListener("DOMContentLoaded", function () {
    const preselectedImage = "{{asset('storage/product')}}/{{$product->cover_image}}"; // Replace with actual image URL from database
    const coverInput = document.getElementById("cover_image");
    const coverFileInfo = document.querySelector(".cover-img"); // Select the input field displaying file name
    const coverPreviewContainer = document.getElementById("coverPreviewContainer");

    function displayPreview(src, type = "image") {
        coverPreviewContainer.innerHTML = ""; // Clear previous preview

        const previewItem = document.createElement("div");
        previewItem.classList.add("preview-item");

        let mediaElement;
        if (type === "image") {
            mediaElement = document.createElement("img");
        } else {
            mediaElement = document.createElement("video");
            mediaElement.controls = true;
        }

        mediaElement.src = src;
        previewItem.appendChild(mediaElement);

        // Add close button
        const closeButton = document.createElement("button");
        closeButton.textContent = "×";
        closeButton.classList.add("close-btn");
        closeButton.addEventListener("click", () => {
            previewItem.remove();
            coverPreviewContainer.style.display = "none"; // Hide container
            coverInput.value = ""; // Reset file input
            coverFileInfo.value = ""; // Clear file name from input field
            coverInput.type = ""; // Trick to force reloading input
            coverInput.type = "file"; // Reset input field
        });

        previewItem.appendChild(closeButton);
        coverPreviewContainer.appendChild(previewItem);
        coverPreviewContainer.style.display = "block"; // Show preview
    }

    // Show preselected image if available
    if (preselectedImage && preselectedImage !== "{{asset('storage/product')}}/") { 
        displayPreview(preselectedImage);
        coverFileInfo.value = preselectedImage.split('/').pop(); // Show filename in input field
    }

    coverInput.addEventListener("change", function (event) {
        if (event.target.files.length === 0) return;

        const file = event.target.files[0];
        const reader = new FileReader();
        reader.onload = function (e) {
            const fileType = file.type.startsWith("image/") ? "image" : "video";

            // **Remove previous image before adding the new one**
            coverPreviewContainer.innerHTML = "";

            displayPreview(e.target.result, fileType);
            coverFileInfo.value = file.name; // Show new file name in input field
        };
        reader.readAsDataURL(file);
    });
});


</script>
<script>
/*multi images*/
const preselectedMedia = [
    @foreach($product->productImage as $image)
        @php
            $fileExtension = pathinfo($image->images, PATHINFO_EXTENSION);
            $mediaType = in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']) ? 'image' : (in_array(strtolower($fileExtension), ['mp4', 'avi', 'mov', 'mkv', 'flv']) ? 'video' : 'unknown');
        @endphp
        { 
            url: "{{ asset('storage/product/' . $image->images) }}", 
            type: "{{ $mediaType }}",
            mediaId: "{{ $image->id }}"
        } @unless($loop->last),@endunless
    @endforeach
];


const selectedFiles = new DataTransfer(); // Keeps track of selected files
const fileUploadInfo = document.querySelector(".multi-product-img");

function updateFileTitle() {
    if (selectedFiles.files.length > 0) {
        fileUploadInfo.value = Array.from(selectedFiles.files).map(file => file.name).join(", ");
    } else {
        fileUploadInfo.value = ""; // Clear input field if no files are left
    }
}

function displayPreview(src, type = "image", mediaId = null, file = null) {
    const previewContainer = document.getElementById("mediaPreviewContainer");
    previewContainer.style.display = "flex"; // Show container

    const previewItem = document.createElement("div");
    previewItem.classList.add("preview-items");

    let mediaElement;
    if (type === "image") {
        mediaElement = document.createElement("img");
    } else {
        mediaElement = document.createElement("video");
        mediaElement.controls = true;
    }

    mediaElement.src = src;
    
    previewItem.setAttribute("data-image-id", mediaId);

    previewItem.appendChild(mediaElement);

    // Add close button
    const closeButton = document.createElement("button");
    closeButton.textContent = "×";
    closeButton.classList.add("close-btn");
    closeButton.addEventListener("click", () => {
        previewItem.remove();

        // Remove file from DataTransfer if it's a newly added file
        if (file) {
            removeFile(file);
        }

        if (previewContainer.children.length === 0) {
            previewContainer.style.display = "none"; // Hide if no items left
        }
    });
    previewItem.appendChild(closeButton);
    previewContainer.appendChild(previewItem);
}

function removeFile(file) {
    const updatedDataTransfer = new DataTransfer();

    for (let i = 0; i < selectedFiles.files.length; i++) {
        if (selectedFiles.files[i] !== file) {
            updatedDataTransfer.items.add(selectedFiles.files[i]);
        }
    }

    selectedFiles.items.clear();
    for (let i = 0; i < updatedDataTransfer.files.length; i++) {
        selectedFiles.items.add(updatedDataTransfer.files[i]);
    }

    document.getElementById("media").files = selectedFiles.files;
    updateFileTitle(); // Update title after file removal
}

// Show preselected media
preselectedMedia.forEach(media => {
    displayPreview(media.url, media.type, media.mediaId);
});

document.getElementById("media").addEventListener("change", function (event) {
    const previewContainer = document.getElementById("mediaPreviewContainer");

    Array.from(event.target.files).forEach(file => {
        const reader = new FileReader();
        reader.onload = function (e) {
            const fileType = file.type.startsWith("image/") ? "image" : "video";
            displayPreview(e.target.result, fileType, file);
        };
        reader.readAsDataURL(file);
        
        selectedFiles.items.add(file);
    });

    document.getElementById("media").files = selectedFiles.files;
    updateFileTitle(); // Update filename input when files are added
});


$(document).ready(function(){

       // Close button event listener
        $(document).on('click', '.close-btn', function() {
            // Get the data-image-id from the closest preview item
            var image_id = $(this).closest('.preview-items').data('image-id');
            $.ajax({
                method: 'GET',
                url: '{{route("delete-product-image")}}',
                data: {
                    _token: "{{ csrf_token() }}", 
                    image_id: image_id,
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

        });

    });

</script>
@endsection