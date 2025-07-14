@extends('admin.layouts.master')
@section('title', 'Help & Support')
@section('content')
<div class="content-wrapper user-manage-box">
    <div class="help-info">
        <div class="help-heading mb-3 mt-4">
            <h3 class="p-h-color fs-clash fw-600">Help &amp; Support</h3>
        </div>
    </div>
    <div class="card col-lg-10 p-3">
        <div class="row gy-lg-0 gy-4">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between">
                        <div class="name-info d-flex">
                            <div class="p-name">
                                <p>Name</p>
                                <p>Email</p>
                                <p>Message</p>
                            </div>
                            <div class="p-n-details">
                                <p><span class="ps-3 pe-3">:</span>{{$ticket_detail->name}}</p>
                                <p><span class="ps-3 pe-3">:</span>{{$ticket_detail->email}}</p>
                                <p><span class="ps-3 pe-3">:</span>{{$ticket_detail->message}}</p>
                            </div>
                        </div>
                        <div class="pending-btn">
                            <button type="button" class="btn btn-primary bg-dc color-dc pe-4 fs-14 rounded-10 fw-500">{{$ticket_detail->status}}</button>
                        </div>
                    </div>

                    <form action="{{route('resolved-ticket')}}" method="post" id="resolvedTicket">
                        @csrf
                        <input type="hidden" name="ticket_id" id="ticket_id" value="{{$ticket_detail->id}}">
                        <div class="col-md-12">
                            <div class="h-solution mt-4">
                                <label for="solution" class="form-label d-block">Solution</label>
                                <textarea placeholder="Write Solution here.." id="solution"  name="solution" class="form-control text-black rounded-10  @error('solution') is-invalid @enderror"  style="height: 100px">  {{$ticket_detail->solutions}}</textarea>                           
                                @error('solution')
                                    <div class="invalid-feedback">{{$message}}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="submit-btn mt-4">
                                <button type="submit" class=" btn btn-primary bg-black text-white px-5 py-2 rounded-8 fw-500">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
           
            <div class="col-md-3">
                
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')

<!-- Include jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>

<script>
    $(document).ready(function() {
        $("textarea#solution").val($.trim($("textarea#solution").val()));
    });
    
    $(document).ready(function () {
        $("#resolvedTicket").validate({
            rules: {
                solution: {
                    required: true, // Ensures the textarea is not empty
                }
            },
            messages: {
                solution: {
                    required: "Solution is required.", // Custom error message for the textarea
                }
            },
            errorPlacement: function (error, element) {
                if (element.prop("tagName").toLowerCase() === "textarea") {
                    error.insertAfter(element); // Ensures error is displayed right after the textarea
                } else {
                    error.insertAfter(element);
                }
            }
        });
    });
</script>


@endsection