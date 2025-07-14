@extends('admin.layouts.master')
@section('title', 'Dahsboard')
@section('content')
<div class="content-wrapper ">
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                <h4 class="card-title">Change Password</h4>
                <form id="change-password" method="post" action="{{route('change-password')}}">
                    @csrf
                    <div class="form-group">
                        <label for="current_password">Old Password</label>
                        <input type="password" class="form-control @error('current_password') is-invalid @enderror" name="current_password" id="current_password" value="{{ old('current_password') }}">
                        @error('current_password')
                            <div class="invalid-feedback">{{$message}}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="password">New Password</label>
                        <input type="password" class="form-control  @error('password') is-invalid @enderror" id="password" name="password" value="{{ old('password') }}">
                        @error('password')
                            <div class="invalid-feedback">
                                {{$message}}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Confirm Password</label>
                        <input type="password" class="form-control  @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation" value="{{ old('password_confirmation') }}">
                        @error('password_confirmation')
                            <div class="invalid-feedback">
                                {{$message}}
                            </div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary mr-2">Submit</button>
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
// Adding a custom validation method for alphanumeric characters
jQuery.validator.addMethod("strongPassword", function(value, element) {
    var regex = new RegExp("^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[@$!%*?&])[A-Za-z\\d@$!%*?&]{8,}$");
    return this.optional(element) || regex.test(value); // Validates alphanumeric + special characters
}, "Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.");

$("#change-password").validate({
  rules: {
    current_password: {
      required: true,
    },
    password: {
      required: true,
      minlength: 8,
      strongPassword: true
    },
    password_confirmation: {
      required: true,
      equalTo: "#password" // Ensures the confirmation matches the password field
    }
  },
  messages: {
    current_password: {
      required: "Old password is required.",
    },
    password: {
        required: "Password is required.",
        minlength: "Password must be at least 8 characters long.",
    },
    password_confirmation: {
      required: "Password confirmation is required.",
      equalTo: "Password confirmation must match the password."
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