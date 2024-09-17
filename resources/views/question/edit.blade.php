@extends('layouts.layoutMaster')

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
                            <label for="question">Question</label>
                            <textarea class="form-control" name="question" rows="3" placeholder="Enter Question">{{ old('question', $data->question) }}</textarea>
                            @error('question')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="image">Image</label>
                            <div id="image-rows">
                                <div class="input-group mb-3">
                                    <input type="file" class="form-control" name="image[]">
                                    <button type="button" class="btn btn-success add-image-row">Add Image</button>
                                </div>
                                @if(!empty($image))
                                    @foreach($image as $image)
                                        <div class="input-group mb-3">
                                            <div class="input_image_div">
                                            <img src="{{ asset('storage/images/' . $image->image_name) }}"
                                                 alt="image" class="input_image">
                                            </div>
                                            <input type="text" class="form-control" value="{{ $image->image_name }}"
                                                   readonly>
                                            <input type="hidden" name="existing_images[]" value="{{ $image->id }}">
                                            <button type="button" class="btn btn-danger remove-image-row" data-image-id="{{ $image->id }}">Remove
                                            </button>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <input type="hidden" name="remove_images" id="remove_images" value="">
                            @error('image')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="answer">Answer</label>
                            <textarea name="answer" class="form-control" rows="3" placeholder="Enter Answer">{{ old('answer', $data->answer) }}</textarea>
                            @error('answer')
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
{{--    <script>--}}
{{--        $(document).ready(function () {--}}
{{--            // Handle adding new image input fields dynamically--}}
{{--            $(document).on('click', '.add-image-row', function () {--}}
{{--                var newRow = `--}}
{{--                    <div class="input-group mb-3">--}}
{{--                        <input type="file" class="form-control" name="image[]">--}}
{{--                        <button type="button" class="btn btn-danger remove-image-row">Remove</button>--}}
{{--                    </div>`;--}}
{{--                $('#image-rows').append(newRow);--}}
{{--            });--}}

{{--            // Handle removing an image row--}}
{{--            $(document).on('click', '.remove-image-row', function () {--}}
{{--                $(this).closest('.input-group').remove();--}}
{{--            });--}}
{{--        });--}}
{{--    </script>--}}
<script>
    $(document).ready(function () {
        // Handle adding new image input fields dynamically
        $(document).on('click', '.add-image-row', function () {
            var newRow = `
                <div class="input-group mb-3">
                    <input type="file" class="form-control" name="image[]">
                    <button type="button" class="btn btn-danger remove-image-row">Remove</button>
                </div>`;
            $('#image-rows').append(newRow);
        });

        // Handle removing an image row
        $(document).on('click', '.remove-image-row', function () {
            var imageId = $(this).data('image-id'); // Check if the image has an ID associated with it
            if (imageId) {
                // Add the image ID to the hidden field if it exists
                const currentValue = $('#remove_images').val() ? $('#remove_images').val().split(',') : [];
                if (!currentValue.includes(imageId.toString())) {
                    currentValue.push(imageId);
                    $('#remove_images').val(currentValue.join(','));
                }
            }
            // Remove the image row from the DOM
            $(this).closest('.input-group').remove();
        });
    });
</script>

    <script>
        $(document).ready(function () {
            $("#question-edit").validate({
                rules: {
                    difficulty: {
                        required: true
                    },
                    question: {
                        required: true,
                        minlength: 10
                    },
                    answer: {
                        required: true,
                        minlength: 5
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
                    answer: {
                        required: "Please provide an answer",
                        minlength: "Your answer must be at least 5 characters long"
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
@endsection


