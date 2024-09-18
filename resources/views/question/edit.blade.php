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
                            <textarea class="form-control latex-editor" name="question" rows="3" placeholder="Enter Question">{{ old('question', htmlspecialchars_decode($data->question)) }}</textarea>
                            @error('question')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="image">Question Images</label>
                            <div id="image-rows">
                                <div class="input-group mb-3">
                                    <input type="file" class="form-control" name="questionimage[]">
                                    <button type="button" class="btn btn-success add-question-image-row">Add Question Image</button>
                                </div>
                                @if(!empty($images))
                                    @foreach($images as $image)
                                        @if(!empty($image->type) && $image->type == 'question')
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
                            <input type="hidden" name="remove_question_images" id="remove_question_images" value="">
                        </div>

                        <div class="form-group">
                            <label for="answer">Answer</label>
                            <textarea name="answer" class="form-control latex-editor" rows="3" placeholder="Enter Answer">{{ old('answer', htmlspecialchars_decode($data->answer)) }}</textarea>
                            @error('answer')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Answer Images (Fixed part) -->
                        <div class="form-group">
                            <label for="answerimage">Answer Images</label>
                            <div id="answer-image-rows">
                                <div class="input-group mb-3">
                                    <input type="file" class="form-control" name="answerimage[]">
                                    <button type="button" class="btn btn-success add-answer-image-row">Add Answer Image</button>
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
        $(document).on('click', '.add-question-image-row', function () {
            var newRow = `
                <div class="input-group mb-3">
                    <input type="file" class="form-control" name="questionimage[]">
                    <button type="button" class="btn btn-danger remove-image-row">Remove</button>
                </div>`;
            $('#image-rows').append(newRow);
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

        // Handle removing an image row
        $(document).on('click', '.remove-image-row', function () {
            var imageId = $(this).data('image-id');
            var targetFieldId = $(this).closest('#image-rows').length > 0 ? '#remove_question_images' : '#remove_answer_images';
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
    <script src="https://cdn.tiny.cloud/1/wjxs8gs2u0a4qac3s0lf3dfs2cwpor8tlwc84wx5u938irjw/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script src="https://www.wiris.net/demo/plugins/app/WIRISplugins.js?viewer=image"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.7/MathJax.js?config=TeX-MML-AM_CHTML"></script>


<!-- Place the following <script> and <textarea> tags your HTML's <body> -->
    <script>
        tinymce.init({
            selector: 'textarea.latex-editor',
            external_plugins: {
                tiny_mce_wiris: '{{url('assets/js/math.js')}}',
            },
            draggable_modal: true,
            extended_valid_elements: "*[.*]",
            plugins: [
                // Core editing features
                'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'image', 'link', 'lists', 'media', 'searchreplace', 'table', 'visualblocks', 'wordcount', 'code',
                // Your account includes a free trial of TinyMCE premium features
                // Try the most popular premium features until Oct 1, 2024:
                'checklist', 'mediaembed', 'casechange', 'export', 'formatpainter', 'pageembed', 'a11ychecker', 'tinymcespellchecker', 'permanentpen', 'powerpaste', 'advtable', 'advcode', 'editimage', 'advtemplate', 'ai', 'mentions', 'tinycomments', 'tableofcontents', 'footnotes', 'mergetags', 'autocorrect', 'typography', 'inlinecss', 'markdown',
            ],
            toolbar: 'tiny_mce_wiris_formulaEditor tiny_mce_wiris_formulaEditorChemistry | undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
            tinycomments_mode: 'embedded',
            tinycomments_author: 'Author name',
            mergetags_list: [
                { value: 'First.Name', title: 'First Name' },
                { value: 'Email', title: 'Email' },
            ],
            ai_request: (request, respondWith) => respondWith.string(() => Promise.reject('See docs to implement AI Assistant')),
            setup: function (editor) {
                editor.on('change', function () {
                    MathJax.Hub.Queue(["Typeset", MathJax.Hub, editor.getContent({format: 'html'})]);
                });
            }
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


