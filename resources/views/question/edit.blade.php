@extends('layouts.layoutMaster')
@section('title',env('WEB_NAME').' | Question Edit')
@section('page-style')
    <style>
        .input_image_div {
            max-width: 41px;
            margin-right: 12px;
        }
        .input_image {
            max-width: -webkit-fill-available;
        }
    </style>
@endsection
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
                <form id="question-edit" action="{{ route('question.update', $data->id) }}" method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="form-group">
                            <label for="std">Year</label>
                            <select name="std" id="std" class="form-control" required>
                                <option value="">Select Year</option>
                                <option value="12" {{ ($data->std == 12) ? 'selected' : '' }}>12<sup>th</sup></option>
                                <option value="11" {{ ($data->std == 11) ? 'selected' : '' }}>11<sup>th</sup></option>
                                <option value="10" {{ ($data->std == 10) ? 'selected' : '' }}>10<sup>th</sup></option>
                                <option value="9" {{ ($data->std == 9) ? 'selected' : '' }}>9<sup>th</sup></option>
                                <option value="8" {{ ($data->std == 8) ? 'selected' : '' }}>8<sup>th</sup></option>
                                <option value="7" {{ ($data->std == 7) ? 'selected' : '' }}>7<sup>th</sup></option>
                                <option value="6" {{ ($data->std == 6) ? 'selected' : '' }}>6<sup>th</sup></option>
                                <option value="5" {{ ($data->std == 5) ? 'selected' : '' }}>5<sup>th</sup></option>
                                <option value="4" {{ ($data->std == 4) ? 'selected' : '' }}>4<sup>th</sup></option>
                                <option value="3" {{ ($data->std == 3) ? 'selected' : '' }}>3<sup>rd</sup></option>
                                <option value="2" {{ ($data->std == 2) ? 'selected' : '' }}>2<sup>nd</sup></option>
                                <option value="1" {{ ($data->std == 1) ? 'selected' : '' }}>1<sup>st</sup></option>
                            </select>
                            @error('std')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="topics">Topics</label>
                            <select name="topics[]" id="topics" class="form-control select2"  multiple required>
                                <option value="">Select Topics</option>
                                @foreach($topics as $topic)
                                    <option {{(in_array($topic->id, json_decode($data->topic_id, true) ?? [])) ? 'selected' : ''}} value="{{$topic->id}}">{{$topic->title}}</option>
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
                                <option value="foundation" {{ $data->difficulty == 'foundation' ? 'selected' : '' }}>
                                    Foundation
                                </option>
                                <option
                                    value="intermediate" {{ $data->difficulty == 'intermediate' ? 'selected' : '' }}>
                                    Intermediate
                                </option>
                                <option value="challenging" {{ $data->difficulty == 'challenging' ? 'selected' : '' }}>
                                    Challenging
                                </option>
                            </select>
                            @error('difficulty')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="code">Code</label>
                            <input type="text" name="code" id="code" class="form-control" value="{{$data->code}}"
                                   placeholder="Enter Code" required>
                            @error('code')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="image">Question Images</label>
                            <div id="image-rows">
                                <div class="input-group">
                                    <input type="file" class="form-control" name="questionimage[]">
                                    <button type="button" class="btn btn-primary add-question-image-row">Add Question Image</button>
                                </div>
                                @if(!empty($images))
                                    @foreach($images as $image)
                                        @if(!empty($image->type) && $image->type == 'question')
                                            <div class="input-group mt-3">
                                                <div class="input_image_div">
                                                    <img src="{{ asset('storage/images/' . $image->image_name) }}" alt="image" class="input_image">
                                                </div>
                                                <input type="text" class="form-control" name="existing_question_images[]" value="{{ $image->image_name }}" readonly>
                                                <input type="hidden" name="existing_images[]" value="{{ $image->id }}">
                                                <button type="button" class="btn btn-danger remove-image-row" data-image-id="{{ $image->id }}">Remove</button>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                            @error('question_images')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                            <input type="hidden" name="remove_question_images" id="remove_question_images" value="">
                        </div>


                        <!-- Solution Images (Fixed part) -->
                        <div class="form-group">
                            <label for="solutionimage">Solution</label>
                            <div id="solution-image-rows">
                                <div class="input-group mb-3">
                                    <input type="file" class="form-control" name="solutionimage[]">
                                    <button type="button" class="btn btn-primary add-solution-image-row">Add Solution Image</button>
                                </div>
                                @if(!empty($images))
                                    @foreach($images as $image)
                                        @if(!empty($image->type) && $image->type == 'solution')
                                            <div class="input-group mb-3">
                                                <div class="input_image_div">
                                                    <img src="{{ asset('storage/images/' . $image->image_name) }}" alt="image" class="input_image">
                                                </div>
                                                <input type="text" class="form-control" value="{{ $image->image_name }}" readonly>
                                                <input type="hidden" name="existing_images[]" value="{{ $image->id }}">
                                                <button type="button" class="btn btn-danger remove-image-row" data-image-id="{{ $image->id }}">Remove</button>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                            <input type="hidden" name="remove_solution_images" id="remove_solution_images" value="">
                        </div>

                        <!-- Answer Images (Fixed part) -->
                        <div class="form-group">
                            <label for="answerimage">Answer</label>
                            <div id="answer-image-rows">
                                <div class="input-group mb-3">
                                    <input type="file" class="form-control" name="answerimage[]">
                                    <button type="button" class="btn btn-primary add-answer-image-row">Add Answer Image</button>
                                </div>
                                @if(!empty($images))
                                    @foreach($images as $image)
                                        @if(!empty($image->type) && $image->type == 'answer')
                                            <div class="input-group mb-3">
                                                <div class="input_image_div">
                                                    <img src="{{ asset('storage/images/' . $image->image_name) }}" alt="image" class="input_image">
                                                </div>
                                                <input type="text" class="form-control" value="{{ $image->image_name }}" readonly>
                                                <input type="hidden" name="existing_images[]" value="{{ $image->id }}">
                                                <button type="button" class="btn btn-danger remove-image-row" data-image-id="{{ $image->id }}">Remove</button>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                            <input type="hidden" name="remove_answer_images" id="remove_answer_images" value="">
                        </div>

                        <div class="form-group">
                            <label for="time">Time (in minutes)</label>
                            <input type="number" name="time" id="time" class="form-control"
                                   placeholder="Enter Time" value="{{$data->time}}" required>
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
<script>
    $(document).ready(function () {

        $('#std').change(function () {
            let selectedStandard = $(this).val();
            if (selectedStandard) {
                $.ajax({
                    url: '{{env('AJAX_URL')}}'+'getTopics',
                    type: 'POST',
                    data: {
                        'std': selectedStandard,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (result) {
                        $('#topics').html(result.data);
                        $topics = $('#topics').val();
                        if ($topics.length == 0) {
                            $('#sub_topics').html('<option value="">Please select a topic first</option>');
                        }
                    },
                    error: function (error) {
                        alert('Something went wrong while fetching topics!');
                    }
                });
            } else {
                $('#topics').html('<option value="">Please select a topic first</option>');
                $('#sub_topics').html('<option value="">Please select a topic first</option>');
            }
        });

        if($('#topics').val()){
            $.ajax({
                url: '{{env('AJAX_URL')}}'+'getSelectedSubTopicData',
                type: 'POST',
                data: {
                    'topic_ids': $('#topics').val(),
                    'selected' :'{{$data->subtopic_id}}',
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
        // Handle adding new image input fields dynamically
        $(document).on('click', '.add-question-image-row', function () {
            var newRow = `
                <div class="input-group mt-3">
                    <input type="file" class="form-control" name="questionimage[]">
                    <button type="button" class="btn btn-danger remove-image-row">Remove</button>
                </div>`;
            $('#image-rows').append(newRow);
        });

        // Handle adding new image input fields for answer images
        $(document).on('click', '.add-answer-image-row', function () {
            var newanswerRow = `
                <div class="input-group mb-3">
                    <input type="file" class="form-control" name="answerimage[]">
                    <button type="button" class="btn btn-danger remove-image-row">Remove</button>
                </div>`;
            $('#answer-image-rows').append(newanswerRow);
        });

        // Handle adding new image input fields for solution images
        $(document).on('click', '.add-solution-image-row', function () {
            var newSolutionRow = `
                <div class="input-group mb-3">
                    <input type="file" class="form-control" name="solutionimage[]">
                    <button type="button" class="btn btn-danger remove-image-row">Remove</button>
                </div>`;
            $('#solution-image-rows').append(newSolutionRow);
        });

        // Handle removing an image row
        $(document).on('click', '.remove-image-row', function () {
            var imageId = $(this).data('image-id');
            var targetFieldId = $(this).closest('#image-rows').length > 0 ? '#remove_question_images' : '#remove_solution_images';
            if (imageId) {
                const currentValue = $(targetFieldId).val() ? $(targetFieldId).val().split(',') : [];
                if (!currentValue.includes(imageId.toString())) {
                    currentValue.push(imageId);
                    $(targetFieldId).val(currentValue.join(','));
                }
            }
            // Remove the image row from the DOM
            $(this).closest('.input-group').remove();
        });
    });
</script>
<!-- Place the first <script> tag in your HTML's <head> -->
    <script src="https://cdn.tiny.cloud/1/qfriyoi7c3pgz0wo25pnp83z6n3l8n2p56ckw8fyjz9oq2a0/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script src="https://www.wiris.net/demo/plugins/app/WIRISplugins.js?viewer=image"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.7/MathJax.js?config=TeX-MML-AM_CHTML"></script>
    <link rel="stylesheet" href="{{url('assets/plugins/select2/css/select2.css')}}">
    <script src="{{url('assets/plugins/select2/js/select2.full.js')}}"></script>
    <script>
        $(document).ready(function () {
            $('.select2').select2()
            $('.select3').select2()
            // Handle adding new image input fields for question images

            $('#topics').change(function() {
                const selectedOptions = $(this).val(); // This should be an array
                $.ajax({
                    url: '{{env('AJAX_URL')}}'+'getSubTopicData',
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
            $("#question-edit").validate({
                rules: {
                    difficulty: {
                        required: true
                    },
                    question: {
                        required: true,
                        minlength: 10
                    },
                },
                messages: {
                    difficulty: {
                        required: "Please select the difficulty level"
                    },
                    question: {
                        required: "Please enter a question",
                        minlength: "Your question must be at least 10 characters long"
                    },
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


