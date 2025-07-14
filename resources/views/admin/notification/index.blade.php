@extends('admin.layouts.master')
@section('title', 'Notifications')
@section('content')
<div class="content-wrapper user-manage-box">
    <div class="help-info">
        <div class="help-heading mb-3 mt-4">
            <h3 class="p-h-color fs-clash fw-600">Notifications</h3>
        </div>
    </div>
    <div class="admin-notification-content-box admin-help-content-box f8-bg rounded-20 py-3 border-e7 ">
        <div class="notify-box">
            <div class="row">
                @foreach($notifications as $notification)
                    <div class="col-md-4 mb-3">
                        <div class="1st-box card d-flex flex-row gap-2 shadow rounded px-2 py-3">
                            <div class="n-img mx-3">
                                <img src="{{asset('assets/images/place.png')}}" class="position-img">
                                <span class="n-circle"></span>
                            </div>
                            <div class="n-content-box">
                                <p class="fs-14 mb-1"><a href="{{route('notification-detail',['notification_id'=>$notification->id])}}">{{$notification->title}}</a></p>
                                <p class="fs-12 m-0 light-gray">
                                @php
                                    $created_at = \Carbon\Carbon::parse($notification->created_at);
                                    $time_ago = $created_at->diffForHumans();
                                @endphp
                                {{ $time_ago }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach 
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>


@endsection