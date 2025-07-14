@extends('admin.layouts.master')
@section('title', 'Dahsboard')
@section('content')
<div class="container-fluid page-body-wrapper full-page-wrapper">
    <div class="row w-100 m-0">
        <div class="content-wrapper full-page-wrapper d-flex align-items-center auth login-bg">
        <div class="card col-lg-4 mx-auto">
            <div class="card-body px-5 py-5">
            <h3 class="card-title text-left mb-3">Reset Password</h3>
            <form action="{{route('admin-reset-password')}}" method="post" id="admin_reset_password">
                @csrf
                <input type="hidden" value="{{$token}}" name="token"> 
                <div class="form-group">
                    <label>Password *</label>
                    <div class="input-group position-relative">
                      <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control password w-100 z-0 p_input @error('password') is-invalid @enderror"
                      />
                      <div onclick="togglePassword(this)" style="cursor: pointer;" class="eye-icon">
                        <i class="fa fa-eye eye"></i>
                      </div>
                    </div>
                    @error('password')
                    <div class="invalid-feedback">
                      {{$message}}
                    </div>
                    @enderror
                  </div>
                  
                  <div class="form-group">
                    <label>Confirm Password *</label>
                    <div class="input-group position-relative">
                      <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        class="form-control password w-100 z-0 p_input @error('password_confirmation') is-invalid @enderror"
                      />
                      <div onclick="togglePassword(this)" style="cursor: pointer;" class="eye-icon">
                        <i class="fa fa-eye eye"></i>
                      </div>
                    </div>
                    @error('password_confirmation')
                    <div class="invalid-feedback">
                      {{$message}}
                    </div>
                    @enderror
                  </div>
                  
                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-block enter-btn">Submit</button>
                </div>
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
$(document).ready(function () {
    // Custom validation method for strong passwords
    jQuery.validator.addMethod("strongPassword", function(value, element) {
        return this.optional(element) || 
               /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/.test(value);
    }, "Password must have at least 8 characters, including one uppercase, one lowercase, one number, and one special character.");

    $("#admin_reset_password").validate({
        rules: {
            password: {
                required: true,
                minlength: 8,
                strongPassword: true
            },
            password_confirmation: {
                required: true,
                equalTo: "#password" // Ensures it matches password
            }
        },
        messages: {
            password: {
                required: "Password is required.",
                minlength: "Password must be at least 8 characters long."
            },
            password_confirmation: {
                required: "Password confirmation is required.",
                equalTo: "Passwords do not match."
            }
        }
    });
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
{{-- login password icon --}}
<script>
    function togglePassword(eyeIconDiv) {
      const input = eyeIconDiv.closest('.input-group').querySelector('input');
      const icon = eyeIconDiv.querySelector('i');
    
      if (input) {
        if (input.type === 'password') {
          input.type = 'text';
          icon.classList.remove('fa-eye');
          icon.classList.add('fa-eye-slash');
        } else {
          input.type = 'password';
          icon.classList.remove('fa-eye-slash');
          icon.classList.add('fa-eye');
        }
      } else {
        console.warn("Password input not found!");
      }
    }
    </script>
    
    
@endsection