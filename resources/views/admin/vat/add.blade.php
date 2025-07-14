@extends('admin.layouts.master')
@section('title', 'Shipping Method')
@section('content')
<div class="content-wrapper user-manage-box">
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Add New Vat Rate</h4>
                    <form id="add-vat-rate" method="post" action="{{route('vat.rates.store')}}">
                        @csrf
                        <div class="form-group">
                            <label for="vat_name">Vat Name</label>
                            <input type="text" class="form-control @error('vat_name') is-invalid @enderror" name="vat_name" id="vat_name" value="{{ old('vat_name') }}">
                            @error('vat_name')
                                <div class="invalid-feedback">{{$message}}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="vat_name">Countries</label>
                            <select id="country_id" name="country_id" class="form-control @error('vat_name') is-invalid @enderror">
                                <option value="">--select--</option>
                                @foreach($countries as $county)
                                    <option value="{{$county->id}}">{{$county->name}}</option>
                                @endforeach
                            </select>
                            @error('country_id')
                                <div class="invalid-feedback">{{$message}}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="vat_rate">Vat Rate(%)</label>
                            <input type="text" class="form-control @error('vat_rate') is-invalid @enderror" name="vat_rate" id="vat_rate" value="{{ old('vat_rate') }}">
                            @error('vat_rate')
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
$('#vat_rate').on('input', function() {
    this.value = this.value.replace(/[^0-9\.]/g, '');    
    // Ensure that only one decimal point is allowed.
    if ((this.value.match(/\./g) || []).length > 1) {
        this.value = this.value.replace(/\.(?=.*\.)/, '');
    }
});


$("#add-vat-rate").validate({
    rules: {
        vat_name: {
            required: true,
        },
        vat_rate: {
            required: true,
        },
        country_id: {
            required: true,
        },
    },
    messages: {
        vat_name: {
            required: "Vat name is required.",
        },
        vat_rate: {
            required: "Vat rate is required.",
        },
        country_id: {
            required: "Country is required.",
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