@extends('admin.layouts.master')
@section('title', 'Shipping Method')
@section('content')
<div class="content-wrapper user-manage-box">
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Update Shipping Method</h4>
                    <form id="add-shipping-method" method="post" action="{{route('shipping-update')}}">
                        @csrf
                        <input type="hidden" name="shipping_method_id" id="shipping_method_id" value="{{$shipping_method->id}}">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" id="name" value="{{$shipping_method->name}}">
                            @error('name')
                                <div class="invalid-feedback">{{$message}}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="price">Price</label>
                            <input type="text" class="form-control @error('price') is-invalid @enderror" name="price" id="price" value="{{$shipping_method->price}}">
                            @error('price')
                                <div class="invalid-feedback">{{$message}}</div>
                            @enderror
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
<!-- Include jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<script>
function disableButton() {
    var submitButton = document.getElementById('submitButton');
    submitButton.disabled = true;
    submitButton.innerHTML = 'Submitting...';
}
</script>

<script>
$('#price').on('input', function() {
    this.value = this.value.replace(/[^0-9\.]/g, '');    
    // Ensure that only one decimal point is allowed.
    if ((this.value.match(/\./g) || []).length > 1) {
        this.value = this.value.replace(/\.(?=.*\.)/, '');
    }
});


$("#add-shipping-method").validate({
    rules: {
        name: {
        required: true,
        },
    },
    messages: {
        name: {
        required: "Name is required.",
        },
    },
    submitHandler: function(form) {
        disableButton();
        form.submit();
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