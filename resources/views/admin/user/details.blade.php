@extends('admin.layouts.master')
@section('title', 'Users')
@section('content')
<div class="content-wrapper">
    <div class="row pt-0">
        <div class="col-md-12">
            <div class="top-titlebar pb-3">
                <h2 class="f-20 bold title-main">User Details</h2>
            </div>
            <div class="white-card card">
                <div class="text-center p-3">
                    <div class="user-img-block py-3 pro-img-box">
                        @if(isset($user->profile_pic) && !empty($user->profile_pic))
                            <img alt="binary" src="{{asset('storage/profile')}}/{{$user->profile_pic}}" width=50 height=50 class="img-fluid rounded-circle profile">
                        @else
                            <img alt="binary" src="{{asset('assets/icons/user-default.jpg')}}" class="img-fluid rounded-circle profile">
                        @endif
                    </div>
                    <div class="user-des mb-3">
                    <h6>{{$user->name}}</h6>
                    <p class="f-13 "><a href="mailto:{{$user->email}}">{{$user->email}}</a></p>
                    </div>
                    <div class="row px-2 py-2">
                    <div class="col-md-6">
                        <div class="four-user-block py-2 my-2 bggray border-0">
                            <h5>Phone</h5>
                            <p class="mb-0">{{$user->phone_code}}{{$user->phone_number}}</p>
                        </div>
                    </div>
                
                    <div class="col-md-6">
                        <div class="four-user-block py-2 my-2 bggray border-0">
                            <h5>Gender</h5>
                            <p class="mb-0">{{$user->gender}}</p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="four-user-block py-2 my-2  bggray border-0">
                            <h5>Status</h5>
                            @if(isset($user->status) && $user->status)
                                <p class="mb-0" style="color: rgb(39, 174, 96);">Active</p>
                            @else
                                <p class="mb-0" style="color: rgb(39, 174, 96);">Inactive</p>
                            @endif
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('scripts')

@endsection