@extends('layouts.layoutMaster')
@section('title',env('WEB_NAME').' | Topic Edit')
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
                        <h1 class="m-0 text-dark">Edit Topic</h1>
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
                <form id="topic-edit" action="{{ route('topic.update', $data->id) }}" method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card-body">

                        <div class="form-group">
                            <label for="std">Standard</label>
                            <select name="std" class="form-control" required>
                                <option value="">Select Standard</option>
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
                            <label for="title">Title</label>
                            <input type="text" name="title" id="title" class="form-control"
                                   placeholder="Enter Title" value="{{ $data->title }}" required>
                            @error('title')
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
            $("#topic-edit").validate({
                rules: {
                    std: {
                        required: true
                    },
                    title: {
                        required: true,
                    },
                },
                messages: {
                    std: {
                        required: "Please select the Standard"
                    },
                    title: {
                        required: "Please enter a Title"
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
@endsection


