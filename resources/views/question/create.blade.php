@extends('layouts.layoutMaster')
@section('title',env('WEB_NAME').' | Question Create')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Question Bank</h1>
                    </div>
                </div>
            </div>
        </div>
        @if (session('success'))
            <div class="alert alert-success alert-dismissible mx-3">
                <div class="d-flex gap-2">
                    <h5><i class="icon fas fa-check"></i></h5>
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="alert  alert-danger alert-dismissible mx-3">
                <div class="d-flex gap-2">
                    <h5><i class="icon fas fa-ban"></i></h5>
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                </div>
            </div>
        @endif

        <section class="content m-2">
            <div class="card card-primary">
                <form id="question-form" action="{{route('question.story')}}" method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="topics">Topics</label>
                            <select name="topics[]" id="topics" class="form-control select2"  multiple required>
                                <option value="">Select Topics</option>
                                @foreach($topics as $topic)
                                    <option {{(old('topics'))? in_array($topic->id, old('topics'))?'selected':'' : ''}} value="{{$topic->id}}">{{$topic->title}}</option>
                                @endforeach
                            </select>
                            @error('topics')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="sub_topics">Sub Topics</label>
                            <select name="sub_topics[]" id="sub_topics" class="form-control select3"  multiple required>
                                <option value="">Select Sub Topics</option>
                            </select>
                            @error('sub_topics')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="difficulty">Difficulty</label>
                            <select name="difficulty" class="form-control" required>
                                <option value="">Select Difficulty</option>
                                <option value="foundation" {{ old('difficulty') == 'foundation' ? 'selected' : '' }}>
                                    Foundation
                                </option>
                                <option value="intermediate" {{ old('difficulty') == 'intermediate' ? 'selected' : '' }}>
                                    Intermediate
                                </option>
                                <option value="challenging" {{ old('difficulty') == 'challenging' ? 'selected' : '' }}>
                                    Challenging
                                </option>
                            </select>
                            @error('difficulty')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="code">Code</label>
                            <input type="text" name="code" id="code" class="form-control"
                                   placeholder="Enter Code" value="{{ old('code') }}" required>
                            @error('code')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="questionimage">Question Image</label>
                            <div id="question-image-rows">
                                <div class="input-group">
                                    <input type="file" class="form-control" name="questionimage[]">
                                    <button type="button" class="btn btn-primary add-question-image-row">Add Question
                                        Image
                                    </button>
                                </div>
                            </div>
                            @error('questionimage')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Answer Image Upload -->
                        <div class="form-group">
                            <label for="answerimage">Solution</label>
                            <div id="answer-image-rows">
                                <div class="input-group mb-3">
                                    <input type="file" class="form-control" name="answerimage[]">
                                    <button type="button" class="btn btn-primary add-answer-image-row">Add Answer
                                        Image
                                    </button>
                                </div>
                            </div>
                            @error('answerimage')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="time">Time (in minutes)</label>
                            <input type="number" name="time" id="time" class="form-control"
                                   placeholder="Enter Time" value="{{ old('time') }}" required>
                            @error('time')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    <div class="card-footer d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection

@section('page-script')
    <!-- Place the first <script> tag in your HTML's <head> -->
    <script src="https://cdn.tiny.cloud/1/qfriyoi7c3pgz0wo25pnp83z6n3l8n2p56ckw8fyjz9oq2a0/tinymce/6/tinymce.min.js"
            referrerpolicy="origin"></script>
    <script src="https://www.wiris.net/demo/plugins/app/WIRISplugins.js?viewer=image"></script>
    <link rel="stylesheet" href="{{url('assets/plugins/select2/css/select2.css')}}">
    <script src="{{url('assets/plugins/select2/js/select2.full.js')}}"></script>
    <script>
        $(document).ready(function () {
            if($('#topics').val()){
                $.ajax({
                    url: "{{ url('getSubTopicData') }}",
                    type: 'POST',
                    data: {
                        'topic_ids': $('#topics').val(), // Send as array
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(result) {
                        $('#sub_topics').html(result.data)
                        $('.select3').select2()
                    },
                    error: function(error) {
                        alert('Something Went Wrong!');
                    }
                });
            }
            $('.select2').select2()
            $('.select3').select2()
            // Handle adding new image input fields for question images

            $('#topics').change(function() {
                const selectedOptions = $(this).val(); // This should be an array
                $.ajax({
                    url: "{{ url('getSubTopicData') }}",
                    type: 'POST',
                    data: {
                        'topic_ids': selectedOptions, // Send as array
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(result) {
                        $('#sub_topics').html(result.data)
                        $('.select3').select2()
                    },
                    error: function(error) {
                        alert('Something Went Wrong!');
                    }
                });
            });



            $(document).on('click', '.add-question-image-row', function () {
                var newQuestionRow = `
                <div class="input-group mt-3">
                    <input type="file" class="form-control" name="questionimage[]">
                    <button type="button" class="btn btn-danger remove-image-row">Remove</button>
                </div>`;
                $('#question-image-rows').append(newQuestionRow);
            });

            // Handle adding new image input fields for answer images
            $(document).on('click', '.add-answer-image-row', function () {
                var newAnswerRow = `
                <div class="input-group mb-3">
                    <input type="file" class="form-control" name="answerimage[]">
                    <button type="button" class="btn btn-danger remove-image-row">Remove</button>
                </div>`;
                $('#answer-image-rows').append(newAnswerRow);
            });

            // Handle removing an image row for both question and answer images
            $(document).on('click', '.remove-image-row', function () {
                $(this).closest('.input-group').remove();
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            $("#question-form").validate({
                rules: {
                    difficulty: {
                        required: true
                    },
                    question: {
                        required: true,
                        minlength: 10
                    },
                    'questionimage[]': {
                        required: true,
                        extension: "jpg,jpeg,png,gif"
                    }
                },
                messages: {
                    difficulty: {
                        required: "Please select the difficulty level"
                    },
                    question: {
                        required: "Please enter a question",
                        minlength: "Your question must be at least 10 characters long"
                    },
                    'questionimage[]': {
                        required: "Please upload at least one question image",
                        extension: "Only image files (jpg, jpeg, png, gif) are allowed"
                    }
                },
                errorElement: 'div',
                errorPlacement: function (error, element) {
                    error.addClass('text-danger');
                    element.closest('.form-group').append(error);
                },
                highlight: function (element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function (element) {
                    $(element).removeClass('is-invalid');
                }
            });

            // Remove image input rows
            $(document).on('click', '.remove-image-row', function () {
                $(this).closest('.input-group').remove();
            });
        });
    </script>
<style>
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #007bff !important;
        border: 1px solid #007bff!important;
        color: #ffffff!important;
    }
</style>
@endsection
