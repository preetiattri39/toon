@extends('admin.layouts.master')
@section('title', 'Notifications')
@section('content')
<div class="content-wrapper user-manage-box">
    <div class="help-info">
        <div class="help-heading mb-3 mt-4">
            <h3 class="p-h-color fs-clash fw-600">Notifications</h3>
        </div>
    </div>
    <div class="card  f8-bg rounded-20 p-4 border-e7 ">
        <div class="notification-title-box">
            <h5 class="fw-600 mb-2">Notifications Title</h5>
            <p class="fw-600 fs-14 mb-4">{{$notification->title}}</p>
            <h6 class="fw-600 mb-2">Notifications Description</h6>
            <p>{{$notification->message}}</p>
        </div>
        <div class="notify-btn-box d-flex gap-3 flex-lg-nowrap flex-wrap">
            <form action="{{route('mark-read')}}" method="post">
                @csrf
                <input type="hidden" name="notification_id" value="{{$notification->id}}">
                <div class="submit-btn mt-4">
                    <button type="submit" class="btn btn-primary text-white rounded-8 fw-500"><i class="fa-solid fa-check-double"></i> Mark as Read</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>


@endsection