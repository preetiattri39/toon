@extends('admin.layouts.master')
@section('title', 'Add Product')
@section('content')
<div class="content-wrapper user-manage-box">
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Add New Product</h4>
                    <form id="add-product" method="post" action="{{route('add-product')}}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="name">Product Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" id="name" value="{{ old('name') }}">
                            @error('name')
                                <div class="invalid-feedback">{{$message}}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="stock_quantity">Quantity</label>
                            <input type="text" class="form-control @error('stock_quantity') is-invalid @enderror" name="stock_quantity" id="stock_quantity" value="{{ old('stock_quantity') }}">
                            @error('stock_quantity')
                                <div class="invalid-feedback">{{$message}}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="regular_price">Regular Price</label>
                            <input type="text" class="form-control @error('regular_price') is-invalid @enderror" name="regular_price" id="regular_price" value="{{ old('regular_price') }}">
                            @error('regular_price')
                                <div class="invalid-feedback">{{$message}}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="discounted_price">Discounted Price</label>
                            <input type="text" class="form-control" name="discounted_price" id="discounted_price" value="{{ old('discounted_price') }}">
                        </div>
                        <div class="form-group">
                            <label for="category">Categories</label>
                            <select class="form-control" id="category" name="category">
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
                        </div>
                        <div class="form-group multi-img">
                            <label>Cover Image</label>
                            <input type="file" name="cover_image" id="cover_image" accept="image/*,video/*" class="file-upload-default">
                            <div class="input-group col-xs-12">
                                <input type="text" id="coverFileInfo" class="form-control file-upload-info" disabled placeholder="Upload Image">
                                <span class="input-group-append">
                                    <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                                </span>
                            </div>
                        </div>
                        <div class="preview-container" id="coverPreviewContainer"></div>
                        <div class="form-group multi-img">
                            <label>Media</label>
                            <input type="file" name="media[]" id="media1" accept="image/*,video/*" class="multiple-file-upload-default" multiple>
                            <div class="input-group col-xs-12">
                                <input type="text" id="fileInfo" class="form-control file-upload-info" disabled placeholder="Upload Image">
                                <span class="input-group-append">
                                    <button class="file-upload-browse-multiple btn btn-primary" type="button">Upload</button>
                                </span>
                            </div>
                        </div>

                        <!-- {{-- --new-- --}}
                        {{-- <div class="row m-0">
                        <div class="form-group col-md-3 px-2 multi-img">
                            <label>Media 1</label>
                            <input type="file" id="media1" accept="image/*,video/*" class="multiple-file-upload-default">
                            <div class="input-group col-xs-12">
                            <input type="text" id="fileInfo1" class="form-control file-upload-info" disabled placeholder="Upload Image">
                            <span class="input-group-append">
                                <button class="file-upload-browse btn btn-primary" type="button">+</button>
                            </span>
                        </div>
                          </div>
                          
                          <div class="form-group col-md-3  px-2 m multi-img">
                            <label>Media 2</label>
                            <input type="file" id="media2" accept="image/*,video/*" class="multiple-file-upload-default">
                            <div class="input-group col-xs-12">
                            <input type="text" id="fileInfo2" class="form-control file-upload-info" disabled placeholder="Upload Image">
                            <span class="input-group-append">
                                <button class="file-upload-browse btn btn-primary" type="button">+</button>
                            </span>
                            </div>
                          </div>
                          
                          <div class="form-group col-md-3 px-2 multi-img">
                            <label>Media 3</label>
                            <input type="file" id="media3" accept="image/*,video/*" class="multiple-file-upload-default">
                            <div class="input-group col-xs-12">
                            <input type="text" id="fileInfo3" class="form-control file-upload-info" disabled placeholder="Upload Image">
                            <span class="input-group-append">
                                <button class="file-upload-browse btn btn-primary" type="button">+</button>
                            </span>
                            </div>
                          </div>
                          
                          <div class="form-group col-md-3 px-2 multi-img">
                            <label>Media 4</label>
                            <input type="file" id="media4" accept="image/*,video/*" class="multiple-file-upload-default">
                            <div class="input-group col-xs-12">
                            <input type="text" id="fileInfo4" class="form-control file-upload-info" disabled placeholder="Upload Image">
                            <span class="input-group-append">
                                <button class="file-upload-browse btn btn-primary" type="button">+</button>
                            </span>
                            </div>
                          </div>
                        </div>
                          <div class="row mb-2 m-0 pre-view-container">
                          <div class="previewContainer col-3" id="previewContainer1"></div>
                          <div class="previewContainer col-3" id="previewContainer2"></div>
                          <div class="previewContainer col-3" id="previewContainer3"></div>
                          <div class="previewContainer col-3" id="previewContainer4"></div>
                        </div> --}} -->
                        <div class="preview-container" id="previewContainer"></div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <div id="wysiwyg">
                                <textarea id="description" name="description" class="form-control rounded-30 bg-f6">
                                </textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="short_description">Short Description</label>                         
                            <textarea id="short_description" name="short_description" class="form-control rounded-30 bg-f6">
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

$("#add-product").validate({
    rules: {
        name: {
            required: true,
        },
        category: {
            required: true,
        },
        cover_image: {
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
        cover_image: {
            required: "Cover image is required.",
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
/*new multimedia */
// const fileInputs = [
//     {
//       input: document.getElementById("media1"),
//       info: document.getElementById("fileInfo1"),
//       preview: document.getElementById("previewContainer1")
//     },
//     {
//       input: document.getElementById("media2"),
//       info: document.getElementById("fileInfo2"),
//       preview: document.getElementById("previewContainer2")
//     },
//     {
//       input: document.getElementById("media3"),
//       info: document.getElementById("fileInfo3"),
//       preview: document.getElementById("previewContainer3")
//     },
//     {
//       input: document.getElementById("media4"),
//       info: document.getElementById("fileInfo4"),
//       preview: document.getElementById("previewContainer4")
//     }
//   ];

//   function updateFileInfo(input, info) {
//     info.value = Array.from(input.files).map(f => f.name).join(", ") || "Upload Image";
//   }

//   function togglePreviewRow() {
//     const previewRow = document.querySelector(".pre-view-container");
//     const hasAnyPreview = fileInputs.some(({ preview }) => preview.children.length > 0);
//     previewRow.style.display = hasAnyPreview ? "flex" : "none";
//   }

//   function removeFileFromInput(fileInput, fileToRemove, previewItem, previewContainer, fileInfo) {
//     const dataTransfer = new DataTransfer();
//     Array.from(fileInput.files).forEach(file => {
//       if (file !== fileToRemove) {
//         dataTransfer.items.add(file);
//       }
//     });

//     fileInput.files = dataTransfer.files;
//     updateFileInfo(fileInput, fileInfo);
//     previewItem.remove();

//     // if (previewContainer.children.length === 0) { --hide section--
//     //   previewContainer.style.display = "none";
//     // }

//     togglePreviewRow();
//   }

//   function displayPreview(src, type, file, fileInput, previewContainer, fileInfo) {
//     previewContainer.style.display = "flex";

//     const previewItem = document.createElement("div");
//     previewItem.classList.add("preview-item");

//     let mediaElement = type === "image" ? document.createElement("img") : document.createElement("video");
//     if (type === "video") mediaElement.controls = true;

//     mediaElement.src = src;
//     previewItem.appendChild(mediaElement);

//     const closeButton = document.createElement("button");
//     closeButton.textContent = "×";
//     closeButton.classList.add("close-btn-1");
//     closeButton.addEventListener("click", () => {
//       removeFileFromInput(fileInput, file, previewItem, previewContainer, fileInfo);
//     });

//     previewItem.appendChild(closeButton);
//     previewContainer.appendChild(previewItem);

//     togglePreviewRow();
//   }

//   fileInputs.forEach(({ input, info, preview }) => {
//     input.addEventListener("change", function () {
//       Array.from(input.files).forEach(file => {
//         const reader = new FileReader();
//         reader.onload = function (e) {
//           const fileType = file.type.startsWith("image/") ? "image" : "video";
//           displayPreview(e.target.result, fileType, file, input, preview, info);
//         };
//         reader.readAsDataURL(file);
//       });
//       updateFileInfo(input, info);
//     });
//   });





//multi media
   const fileInput = document.getElementById("media1");
    const previewContainer = document.getElementById("previewContainer");
    const fileInfo = document.getElementById("fileInfo");

    function updateFileInfo() {
        fileInfo.value = Array.from(fileInput.files).map(f => f.name).join(", ") || "Upload Image";
    }

    function removeFileFromInput(fileToRemove, previewItem) {
        const dataTransfer = new DataTransfer();
        Array.from(fileInput.files).forEach(file => {
            if (file !== fileToRemove) {
                dataTransfer.items.add(file);
            }
        });

        fileInput.files = dataTransfer.files;
        updateFileInfo();

        previewItem.remove();
        if (previewContainer.children.length === 0) previewContainer.style.display = "none";
    }

    function displayPreview(src, type = "image", file = null) {
        previewContainer.style.display = "flex";

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

        const closeButton = document.createElement("button");
        closeButton.textContent = "×";
        closeButton.classList.add("close-btn");
        closeButton.addEventListener("click", () => {
            removeFileFromInput(file, previewItem);
        });

        previewItem.appendChild(closeButton);
        previewContainer.appendChild(previewItem);
    }

    fileInput.addEventListener("change", function (event) {
        Array.from(event.target.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function (e) {
                const fileType = file.type.startsWith("image/") ? "image" : "video";
                displayPreview(e.target.result, fileType, file);
            };
            reader.readAsDataURL(file);
        });
        updateFileInfo();
    });
    /*cover image*/
    const coverInput = document.getElementById("cover_image");
    const coverPreviewContainer = document.getElementById("coverPreviewContainer");
    const coverFileInfo = document.getElementById("coverFileInfo");

    coverInput.addEventListener("change", function (event) {
        coverPreviewContainer.innerHTML = ""; // Clear previous preview

        if (event.target.files.length === 0) {
            coverPreviewContainer.style.display = "none"; // Hide if no file selected
            coverFileInfo.value = "";
            return;
        }

        const file = event.target.files[0]; // Only allow one file (cover image)
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function (e) {
            coverPreviewContainer.style.display = "block"; // Show preview container

            const previewItem = document.createElement("div");
            previewItem.classList.add("preview-item");

            let mediaElement;
            if (file.type.startsWith("image/")) {
                mediaElement = document.createElement("img");
            } else if (file.type.startsWith("video/")) {
                mediaElement = document.createElement("video");
                mediaElement.controls = true;
            }

            mediaElement.src = e.target.result;
            previewItem.appendChild(mediaElement);

            // Add close button
            const closeButton = document.createElement("button");
            closeButton.textContent = "×";
            closeButton.classList.add("close-btn");
            closeButton.addEventListener("click", () => {
                previewItem.remove();
                coverPreviewContainer.style.display = "none"; // Hide container if removed
                coverInput.value = ""; // Reset file input field
                coverFileInfo.value = ""; // Clear file name
            });

            previewItem.appendChild(closeButton);
            coverPreviewContainer.appendChild(previewItem);
            coverFileInfo.value = file.name; // Show filename in input field
        };
        reader.readAsDataURL(file);
    });
</script>
@endsection