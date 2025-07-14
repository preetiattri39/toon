@extends('admin.layouts.master')
@section('title', 'Dahsboard')
@section('content')
<div class="container-fluid page-body-wrapper full-page-wrapper">
    <div class="row w-100 m-0">
        <div class="content-wrapper full-page-wrapper d-flex align-items-center auth login-bg">
        <div class="card col-lg-4 mx-auto">
            <div class="card-body px-5 py-5">
            <h3 class="card-title text-left mb-3">Login</h3>
            <form action="{{route('login')}}" method="post" id="admin_login">
                @csrf
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" id="email" class="form-control p_input @error('email') is-invalid @enderror">
                    @error('email')
                        <div class="invalid-feedback">{{$message}}</div>
                    @enderror
                </div>
                <div class="form-group ">
                    <label>Password *</label>
                    <div class="input-group position-relative">
                        <input type="password" id="password" name="password" class="form-control p_input z-0 @error('password') is-invalid @enderror">
                        <div onclick="togglePassword()"  style="cursor: pointer;" class="eye-icon">
                            <i class="fa fa-eye" id="eye-icon"></i>
                        </div>
                    </div>
                    @error('password')
                        <div class="invalid-feedback d-block">
                            {{$message}}
                        </div>
                    @enderror
                </div>
                <div class="form-group d-flex align-items-center justify-content-between">
                    <div class="form-check">
                        <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="remember" id="remember"> Remember me <i class="input-helper"></i></label>
                    </div>
                    <a href="{{route('admin-forgot-password')}}" class="forgot-pass">Forgot password</a>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-block enter-btn">Login</button>
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

{{-- login password icon --}}
<script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eye-icon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    }
    </script>
 
<script>
$("#admin_login").validate({
  rules: {
    email: {
      required: true,
    },
    password: {
      required: true,
    },
  },
  messages: {
    email: {
      required: "Email is required.",
    },
    password: {
      required: "Password is required.",
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