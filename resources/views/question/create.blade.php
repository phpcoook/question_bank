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
                            <label for="std">Year</label>
                            <select name="std" id="std" class="form-control" required>
                                <option value="">Select Year</option>
                                <option value="12" {{ (old('std') == 12) ? 'selected' : '' }}>12<sup>th</sup></option>
                                <option value="11" {{ (old('std') == 11) ? 'selected' : '' }}>11<sup>th</sup></option>
                                <option value="10" {{ (old('std') == 10) ? 'selected' : '' }}>10<sup>th</sup></option>
                                <option value="9" {{ (old('std') == 9) ? 'selected' : '' }}>9<sup>th</sup></option>
                                <option value="8" {{ (old('std') == 8) ? 'selected' : '' }}>8<sup>th</sup></option>
                                <option value="7" {{ (old('std') == 7) ? 'selected' : '' }}>7<sup>th</sup></option>
                                <option value="6" {{ (old('std') == 6) ? 'selected' : '' }}>6<sup>th</sup></option>
                                <option value="5" {{ (old('std') == 5) ? 'selected' : '' }}>5<sup>th</sup></option>
                                <option value="4" {{ (old('std') == 4) ? 'selected' : '' }}>4<sup>th</sup></option>
                                <option value="3" {{ (old('std') == 3) ? 'selected' : '' }}>3<sup>rd</sup></option>
                                <option value="2" {{ (old('std') == 2) ? 'selected' : '' }}>2<sup>nd</sup></option>
                                <option value="1" {{ (old('std') == 1) ? 'selected' : '' }}>1<sup>st</sup></option>
                            </select>

                            @error('std')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="topics">Topics</label>&nbsp; <input type="checkbox" id="topic_select_box"> Select All
                            <select name="topics[]" id="topics" class="form-control select2" multiple required>
                                <option disabled value="">Select Topics</option>
                            </select>
                            @error('topics')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="sub_topics">Sub Topics</label>&nbsp; <input type="checkbox" id="sub_topic_select_box"> Select All
                            <select name="sub_topics[]" id="sub_topics" class="form-control select3" multiple required>
                                <option disabled value="">Select Sub Topics</option>
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
                                <option
                                    value="intermediate" {{ old('difficulty') == 'intermediate' ? 'selected' : '' }}>
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
                                    <input type="file" class="form-control" name="questionimage[]" id="questionimage">
                                </div>
                            </div>
                            @error('questionimage')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="form-group">
                            <label for="solutionimage">Solution</label>
                            <div id="solution-image-rows">
                                <div class="input-group mb-3">
                                    <input type="file" class="form-control" name="solutionimage[]" id="solutionimage">
                                </div>
                            </div>
                            @error('solutionimage')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Answer Image Upload -->
                        <div class="form-group">
                            <label for="answerimage">Answer</label>
                            <div id="answer-image-rows">
                                <div class="input-group mb-3">
                                    <input type="file" class="form-control" name="answerimage[]" id="answerimage">
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
            $("#topic_select_box").click(function() {
                if ($("#topic_select_box").is(':checked')) {
                    $("#topics > option").each(function() {
                        if (!$(this).is(':disabled')) {
                            $(this).prop("selected", true);
                        }
                    });
                } else {
                    $("#topics > option").prop("selected", false);
                }
                $("#topics").trigger("change");
            });
            $("#sub_topic_select_box").click(function() {
                if ($("#sub_topic_select_box").is(':checked')) {
                    $("#sub_topics > option").each(function() {
                        if (!$(this).is(':disabled')) {
                            $(this).prop("selected", true);
                        }
                    });
                } else {
                    $("#sub_topics > option").prop("selected", false);
                }
                $("#sub_topics").trigger("change");
            });

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

            if ($('#topics').val()) {
                $.ajax({
                    url: '{{env('AJAX_URL')}}'+'getSubTopicData',
                    type: 'POST',
                    data: {
                        'topic_ids': $('#topics').val(), // Send as array
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (result) {
                        $('#sub_topics').html(result.data)
                        $('.select3').select2()
                    },
                    error: function (error) {
                        alert('Something Went Wrong!');
                    }
                });
            }
            $('.select2').select2()
            $('.select3').select2()

            $('#topics').change(function () {
                const selectedOptions = $(this).val(); // This should be an array
                $.ajax({
                    url: '{{env('AJAX_URL')}}'+'getSubTopicData',
                    type: 'POST',
                    data: {
                        'topic_ids': selectedOptions, // Send as array
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (result) {
                        $('#sub_topics').html(result.data)
                        $('.select3').select2()
                    },
                    error: function (error) {
                        alert('Something Went Wrong!');
                    }
                });
            });

            const fileInputs = [
                document.getElementById('questionimage'),
                document.getElementById('solutionimage'),
                document.getElementById('answerimage')
            ];

            let currentInputIndex = 0;

            // Handle paste event for images
            window.addEventListener('paste', function(event) {
                const items = event.clipboardData.items;
                const dataTransfer = new DataTransfer();
                let imagesPasted = false;

                for (let i = 0; i < items.length; i++) {
                    const item = items[i];

                    // Check if the pasted item is an image
                    if (item.type.startsWith('image/')) {
                        const file = item.getAsFile();
                        dataTransfer.items.add(file);
                        imagesPasted = true;
                    }
                }

                // If images are found in the clipboard, add them to the current file input
                if (imagesPasted) {
                    const currentInput = fileInputs[currentInputIndex];
                    const existingFiles = Array.from(currentInput.files);
                    existingFiles.forEach(file => dataTransfer.items.add(file));
                    currentInput.files = dataTransfer.files;

                    alert(`Images pasted into input ${currentInputIndex + 1}!`);

                    // Move to the next file input if the current one has files
                    if (currentInput.files.length > 0) {
                        currentInputIndex++;
                        // Stop if we've processed all inputs
                        if (currentInputIndex >= fileInputs.length) {
                            currentInputIndex = fileInputs.length - 1; // Stay on the last input
                            alert('All inputs are filled!');
                        }
                    }
                }
            });

            document.getElementById('upload-form').addEventListener('submit', function(event) {
                event.preventDefault();

                fileInputs.forEach((input, index) => {
                    const files = input.files;
                    if (files.length > 0) {
                        console.log(`Files uploaded from input ${index + 1}:`, Array.from(files).map(file => file.name));
                    } else {
                        console.log(`No files selected in input ${index + 1}.`);
                    }
                });
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
            border: 1px solid #007bff !important;
            color: #ffffff !important;
        }
    </style>
@endsection
