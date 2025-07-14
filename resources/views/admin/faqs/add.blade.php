@extends('admin.layouts.master')
@section('title', 'Faqs')
@section('content')

<div class="content-wrapper">
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Add New FAQ</h4>
                    <form id="add-faq" method="post" action="{{route('add-faq')}}">
                        @csrf
                        <div class="form-group">
                            <label for="question">Question</label>
                            <input type="text" class="form-control @error('question') is-invalid @enderror" name="question" id="question" value="{{ old('question') }}">
                            @error('question')
                                <div class="invalid-feedback">{{$message}}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="answer">Answer</label>
                            <textarea name="answer" id="answer" class="form-control rounded-30 bg-f6">
                                {{ old('answer') }}    
                            </textarea>
                            @error('answer')
                                <div class="invalid-feedback">{{$answer}}</div>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>

<script>
function disableButton() {
    var submitButton = document.getElementById('submitButton');
    submitButton.disabled = true;
    submitButton.innerHTML = 'Submitting...';
}
</script>

<script>
    $("#add-faq").validate({
        rules: {
            question: {
                required: true,
            },
            answer: {
                required: true,
            }
        },
        messages: {
            question: {
                required: "Question is required.",
            },
            answer: {
                required: "Answer is required.",
            }
        },
        submitHandler: function(form) {
            disableButton();
            form.submit();
        }
    });

    $(document).ready(function(){
        var trimmedDescription = $("#answer").val().trim();
        $("#answer").val(trimmedDescription);
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