@extends('admin.layouts.master')
@section('title', 'Dahsboard')
@section('content')
<div class="content-wrapper ">
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                <h4 class="card-title">Profile Settings</h4>
                <form id="profile-setting" method="post" action="{{route('admin-profile')}}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" id="name" value="{{$admin_user->name}}">
                        @error('name')
                            <div class="invalid-feedback">{{$message}}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="email">Email address</label>
                        <input type="email" class="form-control" id="email" placeholder="Email" disabled value="{{$admin_user->email}}">
                    </div>
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select class="form-control @error('gender') is-invalid @enderror" id="gender" name="gender">
                            <option value="">--Select--</option>
                            <option value="male" {{ isset($admin_user->gender) && $admin_user->gender == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ isset($admin_user->gender) && $admin_user->gender == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ isset($admin_user->gender) && $admin_user->gender == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender')
                            <div class="invalid-feedback">
                                {{$message}}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <img src="{{asset('storage/profile')}}/{{$admin_user->profile_pic}}" width=50 height=50>
                        <label>File upload</label>
                        <input type="file" name="profile_pics" accept="image/*" id="profile_pics" class="file-upload-default">
                        <div class="input-group col-xs-12">
                            <input type="text" name="profile_pics" id="profile_pics" class="form-control file-upload-info" disabled="" placeholder="Upload Image">
                            <span class="input-group-append">
                            <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                            </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="phone_number">Phone Number</label>
                        <input type="text" class="form-control  @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" value="{{$admin_user->phone_number}}">
                        @error('phone_number')
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
$("#profile-setting").validate({
  rules: {
    name: {
      required: true,
    },
    gender: {
      required: true,
    },
    phone_number: {
      required: true,
      digits: true,
      maxlength: 12,
      minlength: 10,
    },
  },
  messages: {
    name: {
      required: "Name is required.",
    },
    gender: {
        required: "Gender is required.",
    },
    phone_number: {
      required: "Phone number is required.",
      digits: "Phone number must contain only numbers.",
    },
  }
});

// Restrict phone number input to numbers only
$('#phone_number').on('input', function() {
    this.value = this.value.replace(/[^0-9+\-]/g, '');
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