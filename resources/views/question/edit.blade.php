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
        ::file-selector-button {
            display: none;
        }
    </style>
    <style>
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #007bff !important;
            border: 1px solid #007bff !important;
            color: #ffffff !important;
        }
    </style>
    <style>
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            max-width: 500px;
            right: 0;
            width: 100%;
            max-height: 500px;
            padding: 0;
            margin: 0 auto;
            height: fit-content;
            background-color: rgb(244 246 249);
            z-index: 999999;
            box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
        }
        .modal-content {
            margin: auto;
            display: block;
            width: 100%;
            max-width: 700px;
        }
        .close {
            position: absolute;
            top: 15px;
            right: 15px;
            color: #343a40;
            font-size: 30px;
            font-weight: bold;
            cursor: pointer;
            opacity: 1;
            width: 30px;
            height: 30px;
            background-color: #ffffff;
            z-index: 2;
            text-align: center;
            border-radius: 50%;
            line-height: 28px;
            text-shadow: none;
            box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
        }
        .close:hover,
        .close:focus {
            color: red;
            text-decoration: none;
            cursor: pointer;
            opacity: 1;
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
                            <select name="std" id="std" class="form-control" required tabindex="1">
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
                            <div id="topics" class="mx-3">
                                @foreach($topics as $topic)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="topics[]" value="{{ $topic->id }}" id="topic_{{ $topic->id }}"
                                            {{ (in_array($topic->id, json_decode($data->topic_id, true) ?? [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="sub_topic_{{ $topic->id }}">{{ $topic->title }}</label>
                                    </div>
                                @endforeach
                            </div>
                            @error('topics')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="sub_topics">Sub Topics</label>
                            <div id="sub_topics" class="mx-3"></div>
                            @error('sub_topics')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="difficulty">Difficulty</label>
                            <select name="difficulty" class="form-control" required tabindex="6">
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
                                   placeholder="Enter Code" required tabindex="7">
                            @error('code')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="image">Question Images</label>
                            <div id="image-rows">
                                <div class="input-group">
                                    <input type="file" class="form-control" name="questionimage[]" id="question" tabindex="8">
                                    <button type="button" class="btn btn-primary add-question-image-row">Add Question Image</button>
                                </div>
                                @if(!empty($images))
                                    @foreach($images as $image)
                                        @if(!empty($image->type) && $image->type == 'question')
                                            <div class="input-group mt-3">
                                                <div class="input_image_div">
                                                    <img src="{{ asset('storage/images/' . $image->image_name) }}" alt="image" class="input_image" onclick="openImageModal(this)">
                                                </div>
                                                <input type="text" class="form-control" name="existing_question_images[]" value="{{ $image->image_name }}" readonly>
                                                <input type="hidden" name="existing_images[]" value="{{ $image->id }}">
                                                <button type="button" class="btn btn-danger remove-image-row" data-image-id="{{ $image->id }}">Remove</button>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                                <div class="image-preview mt-3" id="question-image"></div>
                            </div>
                            @error('question_images')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                            <input type="hidden" name="remove_question_images" id="remove_question_images" value="">
                        </div>

                        <!-- Solution Images -->
                        <div class="form-group">
                            <label for="solutionimage">Solution</label>
                            <div id="solution-image-rows">
                                <div class="input-group mb-3">
                                    <input type="file" class="form-control" name="solutionimage[]" tabindex="9" id="solution">
                                    <button type="button" class="btn btn-primary add-solution-image-row">Add Solution Image</button>
                                </div>
                                @if(!empty($images))
                                    @foreach($images as $image)
                                        @if(!empty($image->type) && $image->type == 'solution')
                                            <div class="input-group mb-3">
                                                <div class="input_image_div">
                                                    <img src="{{ asset('storage/images/' . $image->image_name) }}" alt="image" class="input_image" onclick="openImageModal(this)">
                                                </div>
                                                <input type="text" class="form-control" value="{{ $image->image_name }}" readonly>
                                                <input type="hidden" name="existing_images[]" value="{{ $image->id }}">
                                                <button type="button" class="btn btn-danger remove-image-row" data-image-id="{{ $image->id }}">Remove</button>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                                <div id="solution-image"></div>
                            </div>
                            <input type="hidden" name="remove_solution_images" id="remove_solution_images" value="">
                        </div>

                        <!-- Answer Images -->
                        <div class="form-group">
                            <label for="answerimage">Answer</label>
                            <div id="answer-image-rows">
                                <div class="input-group mb-3">
                                    <input type="file" class="form-control" name="answerimage[]" tabindex="10" id="answer">
                                    <button type="button" class="btn btn-primary add-answer-image-row">Add Answer Image</button>
                                </div>
                                @if(!empty($images))
                                    @foreach($images as $image)
                                        @if(!empty($image->type) && $image->type == 'answer')
                                            <div class="input-group mb-3">
                                                <div class="input_image_div">
                                                    <img src="{{ asset('storage/images/' . $image->image_name) }}" alt="image" class="input_image" onclick="openImageModal(this)">
                                                </div>
                                                <input type="text" class="form-control" value="{{ $image->image_name }}" readonly>
                                                <input type="hidden" name="existing_images[]" value="{{ $image->id }}">
                                                <button type="button" class="btn btn-danger remove-image-row" data-image-id="{{ $image->id }}">Remove</button>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                                <div class="image-preview" id="answer-image"></div>
                            </div>
                            <input type="hidden" name="remove_answer_images" id="remove_answer_images" value="">
                        </div>

                        <div class="form-group">
                            <label for="time">Time (in minutes)</label>
                            <input type="number" name="time" id="time" class="form-control"
                                   placeholder="Enter Time" value="{{$data->time / 60}}" required tabindex="11">
                            @error('time')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    <div class="card-footer d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>

                <!-- Add a modal for displaying images in popup -->
                <div id="imageModal" class="modal" style="display: none;">
                    <span class="close">&times;</span>
                    <img class="modal-content" id="popupImage">
                </div>

                <div id="demo-modal" class="modal">
                    <div class="modal__content">
                        <h1>CSS Only Modal</h1>

                        <p>
                            You can use the :target pseudo-class to create a modals with Zero JavaScript. Enjoy!
                        </p>

                        <div class="modal__footer">
                            Made with <i class="fa fa-heart"></i>, by <a href="https://twitter.com/denicmarko" target="_blank">@denicmarko</a>
                        </div>

                        <a href="#" class="modal__close">&times;</a>
                    </div>
                </div>

            </div>
        </section>
    </div>
@endsection

@section('page-script')
    <script>
        document.querySelectorAll('#question, #answer, #solution').forEach(function(element) {
            element.addEventListener('click', function(event) {
                event.preventDefault();
            });
        });

        $(document).ready(function () {
            $('#std').change(function () {
                let selectedStandard = $(this).val();
                if (selectedStandard) {
                    $.ajax({
                        url: '{{env('AJAX_URL')}}' + 'getTopics',
                        type: 'POST',
                        data: {
                            'std': selectedStandard,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (result) {
                            $('#topics').html(result.data);
                            $('#sub_topics').html('<p>No SubTopics available.</p>');
                        },
                        error: function (error) {
                            alert('Something went wrong while fetching topics!');
                        }
                    });
                } else {
                    $('#topics').html('<p>No Topics available.</p>');
                    $('#sub_topics').html('<p>No SubTopics available.</p>');
                }
            });

            if ($('input[name="topics[]"]:checked').length > 0) {
                let selectedTopics = $('input[name="topics[]"]:checked').map(function() {
                    return $(this).val();
                }).get();

                $.ajax({
                    url: '{{env('AJAX_URL')}}' + 'getSelectedSubTopicData',
                    type: 'POST',
                    data: {
                        'topic_ids':selectedTopics,
                        'selected': '{{$data->subtopic_id}}',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (result) {
                        $('#sub_topics').html(result.data)
                    },
                    error: function (error) {
                        alert('Something Went Wrong!');
                    }
                });
            }

            const previews = [
                document.getElementById('question-image'),
                document.getElementById('solution-image'),
                document.getElementById('answer-image')
            ];

            let currentInput = null;

            $(document).on('focus', 'input[type="file"]', function () {
                currentInput = this;
            });

            // Function to handle adding new image input fields dynamically
            function addImageRow(containerId, name, buttonClass) {
                $(document).on('click', buttonClass, function () {
                    var newRow = `
        <div class="input-group mb-3">
            <input type="file" class="form-control" name="${name}[]">
            <button type="button" class="btn btn-danger remove-image-row">Remove</button>
        </div>`;
                    $(containerId).append(newRow);
                });
            }
            addImageRow('#image-rows', 'questionimage', '.add-question-image-row');
            addImageRow('#solution-image-rows', 'solutionimage', '.add-solution-image-row');
            addImageRow('#answer-image-rows', 'answerimage', '.add-answer-image-row');


            // Handle removing an image row and the associated preview
            $(document).on('click', '.remove-image-row', function () {
                var imageId = $(this).data('image-id');
                var filename = $(this).data('filename');
                var filedName = $(this).data('filedname');

                var targetFieldId = $(this).closest('#image-rows').length > 0 ? '#remove_question_images' :
                    $(this).closest('#solution-image-rows').length > 0 ? '#remove_solution_images' : '#remove_answer_images';
                if (imageId) {
                    const currentValue = $(targetFieldId).val() ? $(targetFieldId).val().split(',') : [];
                    if (!currentValue.includes(imageId.toString())) {
                        currentValue.push(imageId);
                        $(targetFieldId).val(currentValue.join(','));
                    }
                }

                // Remove the image from the input file field based on filename
                var inputElement = document.getElementById(filedName); // Get the input file element by id
                if (inputElement && inputElement.files) {
                    var dataTransfer = new DataTransfer(); // Create a new DataTransfer object

                    for (let i = 0; i < inputElement.files.length; i++) {
                        // Keep all files except the one that matches the filename to be removed
                        if (inputElement.files[i].name !== filename) {
                            dataTransfer.items.add(inputElement.files[i]);
                        }
                    }

                    // Update the input file field with the new FileList
                    inputElement.files = dataTransfer.files;
                }

                // Remove the image row from the DOM
                $(this).closest('.input-group').remove();
                // Remove the preview image container
                $(this).closest('.file-preview').remove();
            });

            // Function to update the preview with images
            function updatePreview(previewContainer, files, currentInputId) {
                previewContainer.innerHTML = '';

                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    // Create a container for each file preview
                    const fileContainer = document.createElement('div');
                    fileContainer.classList.add('file-preview');
                    fileContainer.style.display = 'flex';
                    fileContainer.style.alignItems = 'center';
                    fileContainer.style.marginBottom = '10px';

                    // Create and configure the image element
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.style.maxWidth = '39px';
                    img.style.marginRight = '7px';

                    // Create and configure the input element for the file name
                    const input = document.createElement('input');
                    input.type = 'text';
                    input.value = file.name;
                    input.readOnly = true;
                    input.style.marginLeft = '10px';
                    input.classList.add('form-control');

                    // Create a remove button for the image preview
                    const removeButton = document.createElement('button');
                    removeButton.type = 'button';
                    removeButton.setAttribute('data-imageId',i);
                    removeButton.setAttribute('data-fileName', file.name);
                    removeButton.setAttribute('data-filedName', currentInputId);
                    removeButton.classList.add('btn', 'btn-danger', 'remove-image-row');
                    removeButton.textContent = 'Remove';

                    // Append image, input, and remove button to the file container
                    fileContainer.appendChild(img);
                    fileContainer.appendChild(input);
                    fileContainer.appendChild(removeButton);

                    // Append the file container to the preview container
                    previewContainer.appendChild(fileContainer);

                    // Add event listener to the remove button
                    removeButton.addEventListener('click', function () {
                        // Remove the file from the DataTransfer object and input.files
                        const updatedFiles = Array.from(currentInput.files).filter((f) => f !== file);
                        const newDataTransfer = new DataTransfer();
                        updatedFiles.forEach(f => newDataTransfer.items.add(f));
                        currentInput.files = newDataTransfer.files;

                        // Remove the file container from the preview
                        fileContainer.remove();
                    });
                }
            }

            // Example usage:
            const previewContainer = document.getElementById('answer-image');
            const inputFile = document.querySelector('input[type="file"]');


            inputFile.addEventListener('change', (event) => {
                const files = event.target.files;
                updatePreview(previewContainer, files,'answer-image');
            });



            // Handle paste event for images
            window.addEventListener('paste', function (event) {
                const items = event.clipboardData.items;
                const dataTransfer = new DataTransfer();
                let imagesPasted = false;

                // Loop through clipboard items to check if any images are pasted
                for (let i = 0; i < items.length; i++) {
                    const item = items[i];

                    if (item.type.startsWith('image/')) {
                        const file = item.getAsFile();
                        if (file) {
                            dataTransfer.items.add(file);
                            imagesPasted = true;
                        }
                    }
                }

                if (imagesPasted) {
                    const existingFiles = Array.from(currentInput.files);
                    existingFiles.forEach(file => dataTransfer.items.add(file));
                    currentInput.files = dataTransfer.files;

                    // Update preview based on the input index
                    const inputIndex = Array.from(document.querySelectorAll('input[type="file"]')).indexOf(currentInput);
                    updatePreview(previews[inputIndex], dataTransfer.files,currentInput.id);
                }
            });

            // Handle form submission to log the uploaded files
            document.getElementById('upload-form').addEventListener('submit', function (event) {
                event.preventDefault();
                let fileInputs = [...document.querySelectorAll('input[type="file"]')];

                fileInputs.forEach((input) => {
                    const files = input.files;
                    if (files.length > 0) {
                        console.log(`Files uploaded:`, Array.from(files).map(file => file.name));
                    } else {
                        console.log(`No files selected in this input.`);
                    }
                });
            });
        });
        // Load subtopics based on the selected topics
        $(document).on('change', 'input[name="topics[]"]', function () {
            let selectedTopics = $('input[name="topics[]"]:checked').map(function () {
                return $(this).val();
            }).get();

            if (selectedTopics.length > 0) {
                $.ajax({
                    url: '{{env('AJAX_URL')}}' + 'getSubTopicData',
                    type: 'POST',
                    data: {
                        'topic_ids': selectedTopics,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (result) {
                        $('#sub_topics').html(result.data);
                    },
                    error: function () {
                        alert('Something Went Wrong!');
                    }
                });
            } else {
                $('#sub_topics').html('<p>No SubTopics available.</p>');
            }
        });
    </script>
    <!-- Place the first <script> tag in your HTML's <head> -->
    <script src="https://cdn.tiny.cloud/1/qfriyoi7c3pgz0wo25pnp83z6n3l8n2p56ckw8fyjz9oq2a0/tinymce/6/tinymce.min.js"
            referrerpolicy="origin"></script>
    <script src="https://www.wiris.net/demo/plugins/app/WIRISplugins.js?viewer=image"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.7/MathJax.js?config=TeX-MML-AM_CHTML"></script>
    <link rel="stylesheet" href="{{url('assets/plugins/select2/css/select2.css')}}">
    <script src="{{url('assets/plugins/select2/js/select2.full.js')}}"></script>
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
    <script>
        // JavaScript function to handle image click
        function openImageModal(imageElement) {
            var modal = document.getElementById("imageModal");
            var modalImg = document.getElementById("popupImage");

            // Set the clicked image as modal content
            modal.style.display = "block";
            modalImg.src = imageElement.src;
        }

        // Close the modal when the user clicks on <span> (x)
        var closeBtn = document.getElementsByClassName("close")[0];
        closeBtn.onclick = function() {
            document.getElementById("imageModal").style.display = "none";
        }

        // Also close modal on outside click
        window.onclick = function(event) {
            var modal = document.getElementById("imageModal");
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
@endsection


