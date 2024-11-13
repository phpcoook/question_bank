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
                            <label for="std">Year</label>
                            <select name="std" class="form-control" required>
                                <option value="">Select Year</option>
                                <option value="7_maths" {{ ($data->std == '7_maths') ? 'selected' : '' }}>Year 7 Maths</option>
                                <option value="8_maths" {{ ($data->std == '8_maths') ? 'selected' : '' }}>Year 8 Maths</option>
                                <option value="9_maths" {{ ($data->std == '9_maths') ? 'selected' : '' }}>Year 9 Maths</option>
                                <option value="10_maths" {{ ($data->std == '10_maths') ? 'selected' : '' }}>Year 10 Maths</option>
                                <option value="11_standard_maths" {{ ($data->std == '11_standard_maths') ? 'selected' : '' }}>Year 11 Standard Maths</option>
                                <option value="11_2u_maths" {{ ($data->std == '11_2u_maths') ? 'selected' : '' }}>Year 11 2U Maths</option>
                                <option value="11_3u_maths" {{ ($data->std == '11_3u_maths') ? 'selected' : '' }}>Year 11 3U Maths</option>
                                <option value="12_standard_1_maths" {{ ($data->std == '12_standard_1_maths') ? 'selected' : '' }}>Year 12 Standard 1 Maths</option>
                                <option value="12_standard_2_maths" {{ ($data->std == '12_standard_2_maths') ? 'selected' : '' }}>Year 12 Standard 2 Maths</option>
                                <option value="12_2u_maths" {{ ($data->std == '12_2u_maths') ? 'selected' : '' }}>Year 12 2U Maths</option>
                                <option value="12_3u_maths" {{ ($data->std == '12_3u_maths') ? 'selected' : '' }}>Year 12 3U Maths</option>
                                <option value="12_4u_maths" {{ ($data->std == '12_4u_maths') ? 'selected' : '' }}>Year 12 4U Maths</option>
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
                        required: "Please select the Year"
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


