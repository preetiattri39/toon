@extends('admin.layouts.master')
@section('title', 'Dahsboard')
@section('content')
<div class="container-fluid page-body-wrapper full-page-wrapper">
    <div class="row w-100 m-0">
        <div class="content-wrapper full-page-wrapper d-flex align-items-center auth login-bg">
        <div class="card col-lg-4 mx-auto">
            <div class="card-body px-5 py-5">
            <h3 class="card-title text-left mb-3">Forgot Password</h3>
            <form action="{{route('admin-forgot-password')}}" method="post" id="admin_forgot_password">
                @csrf
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" id="email" class="form-control p_input @error('email') is-invalid @enderror">
                    @error('email')
                        <div class="invalid-feedback">{{$message}}</div>
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

<script>
$("#admin_forgot_password").validate({
  rules: {
    email: {
      required: true,
    }
  },
  messages: {
    email: {
      required: "Email is required.",
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