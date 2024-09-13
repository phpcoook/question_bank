@extends('layouts.layoutMaster')

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
                            <label for="difficulty">Difficulty</label>
                            <select name="difficulty" class="form-control" required>
                                <option value="">Select Difficulty</option>
                                <option value="foundation">Foundation</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="challenging">Challenging</option>
                            </select>
                            @error('difficulty')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="code">Code</label>
                            <input type="text" name="code" id="code" class="form-control"
                                   placeholder="Enter Code" required>
                            @error('code')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="question">Question</label>
                            <textarea class="form-control" rows="3" name="question" placeholder="Enter ..."></textarea>
                            @error('question')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="image">Image</label>
                            <div id="image-rows">
                                <div class="input-group">
                                    <input type="file" class="form-control" name="image[]">
                                    <button type="button" class="btn btn-success add-image-row">Add Image</button>
                                </div>
                            </div>
                            @error('image')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="answer">Answer</label>
                            <textarea class="form-control" rows="3" name="answer" placeholder="Enter ..."></textarea>
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
                    "image[]": {
                        required: true,
                        extension: "jpg|jpeg|png|gif"
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
                    "image[]": {
                        required: "Please upload an image",
                        extension: "Please upload a valid image file (jpg, jpeg, png, gif)"
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
