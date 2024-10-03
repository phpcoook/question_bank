@extends('layouts.layoutMaster')
@section('title',env('WEB_NAME').' | Pricing Edit')
@section('page-style')
@endsection
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Edit Pricing</h1>
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
                <form id="pricing-edit" action="{{ route('pricing.update', $data->id) }}" method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card-body">


                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" name="title" id="title" class="form-control" value="{{$data->title}}"
                                   placeholder="Enter Title" required>
                            @error('title')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="price">Price</label>
                            <input type="number" name="price" id="price" class="form-control"
                                   placeholder="Enter Price" value="{{$data->price}}" required>
                            @error('price')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="price">Detail</label>
                            <textarea  class="editor" name="detail" placeholder="Enter Detail ...">{{$data->detail}}</textarea>
                            @error('detail')
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
        $(document).on('click', '.add-pricing-image-row', function () {
            var newRow = `
                <div class="input-group mb-3">
                    <input type="file" class="form-control" name="pricingimage[]">
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
            var targetFieldId = $(this).closest('#image-rows').length > 0 ? '#remove_pricing_images' : '#remove_answer_images';
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
<script>
        $(document).ready(function () {
            $("#pricing-edit").validate({
                rules: {
                    title: {
                        required: true
                    },
                    price: {
                        required: true,
                    }
                },
                messages: {
                    title: {
                        required: "Title is required"
                    },
                    price: {
                        required: "Price is required"
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
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.0/classic/ckeditor.js"></script>

<script>
    ClassicEditor
        .create(document.querySelector('.editor'))
        .then(editor => {
            console.log(editor);
        })
        .catch(error => {
            console.error(error);
        });
</script>
@endsection


