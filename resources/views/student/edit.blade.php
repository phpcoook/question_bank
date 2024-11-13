@extends('layouts.layoutMaster')
@section('title',env('WEB_NAME').' | Student Edit')
@section('page-style')
    <style>
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #007bff !important;
            border: 1px solid #007bff !important;
            color: #ffffff !important;
        }
    </style>
    <link rel="stylesheet" href="{{url('assets/plugins/select2/css/select2.css')}}">
@endsection
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Student Edit</h1>
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
                <form id="student-create" action="{{ route('student.update', $data->id) }}" method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="form-group">
                            <label for="std">Year</label>
                            @php
                                $cleanedStd = trim($data->std, '[]');
                                $stdArray = is_array($data->std) ? $data->std : explode(',', $cleanedStd);
                                $stdArray = array_map(function($item) {
                                    return trim($item, '"');
                                }, $stdArray);
                            @endphp
                            <select name="std[]" id="std" class="form-control select2" multiple required tabindex="1">
                                <option value="">Select Year</option>
                                <option value="7_maths" {{ in_array('7_maths', $stdArray) ? 'selected' : '' }}>Year 7 Maths</option>
                                <option value="8_maths" {{ in_array('8_maths', $stdArray) ? 'selected' : '' }}>Year 8 Maths</option>
                                <option value="9_maths" {{ in_array('9_maths', $stdArray) ? 'selected' : '' }}>Year 9 Maths</option>
                                <option value="10_maths" {{ in_array('10_maths', $stdArray) ? 'selected' : '' }}>Year 10 Maths</option>
                                <option value="11_standard_maths" {{ in_array('11_standard_maths', $stdArray) ? 'selected' : '' }}>Year 11 Standard Maths</option>
                                <option value="11_2u_maths" {{ in_array('11_2u_maths', $stdArray) ? 'selected' : '' }}>Year 11 2U Maths</option>
                                <option value="11_3u_maths" {{ in_array('11_3u_maths', $stdArray) ? 'selected' : '' }}>Year 11 3U Maths</option>
                                <option value="12_standard_1_maths" {{ in_array('12_standard_1_maths', $stdArray) ? 'selected' : '' }}>Year 12 Standard 1 Maths</option>
                                <option value="12_standard_2_maths" {{ in_array('12_standard_2_maths', $stdArray) ? 'selected' : '' }}>Year 12 Standard 2 Maths</option>
                                <option value="12_2u_maths" {{ in_array('12_2u_maths', $stdArray) ? 'selected' : '' }}>Year 12 2U Maths</option>
                                <option value="12_3u_maths" {{ in_array('12_3u_maths', $stdArray) ? 'selected' : '' }}>Year 12 3U Maths</option>
                                <option value="12_4u_maths" {{ in_array('12_4u_maths', $stdArray) ? 'selected' : '' }}>Year 12 4U Maths</option>
                            </select>
                            @error('std')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <input type="text" name="first_name" id="first_name" class="form-control"
                                   placeholder="Enter First Name" value="{{ old('first_name', $data->first_name) }}"
                                   required>
                            @error('first_name')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <input type="text" name="last_name" id="last_name" class="form-control"
                                   placeholder="Enter Last Name" value="{{ old('last_name', $data->last_name) }}"
                                   required>
                            @error('last_name')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="Enter Email"
                                   value="{{ old('email', $data->email) }}" required>
                            @error('email')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password">Change Password</label>
                            <input type="password" name="password" id="password" class="form-control"
                                   placeholder="Enter Password">
                            @error('password')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="form-group">
                            <label for="date_of_birth">Date of Birth</label>
                            <input type="date" name="date_of_birth" id="date_of_birth" class="form-control"
                                   value="{{ old('date_of_birth', $data->date_of_birth) }}" required>
                            @error('date_of_birth')
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
    <script src="{{url('assets/plugins/select2/js/select2.full.js')}}"></script>
    <script>
        $(document).ready(function () {
            $("#student-create").validate({
                rules: {
                    first_name: {
                        required: true,
                        minlength: 2
                    },
                    last_name: {
                        required: true,
                        minlength: 2
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    grade: {
                        required: true,
                        digits: true,
                        minlength: 1,
                        maxlength: 2
                    },
                    date_of_birth: {
                        required: true,
                        date: true
                    },
                    std: {
                        required: true
                    }
                },
                messages: {
                    first_name: {
                        required: "Please enter the first name",
                        minlength: "First name must be at least 2 characters long"
                    },
                    last_name: {
                        required: "Please enter the last name",
                        minlength: "Last name must be at least 2 characters long"
                    },
                    email: {
                        required: "Please enter an email address",
                        email: "Please enter a valid email address"
                    },
                    grade: {
                        required: "Please enter the grad",
                        digits: "Please enter a valid number",
                        minlength: "Grad must be at least 1 digit long",
                        maxlength: "Grad must not exceed 2 digits"
                    },
                    date_of_birth: {
                        required: "Please select the date of birth",
                        date: "Please enter a valid date"
                    },
                    std: {
                        required: "Please select the Year"
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
        });
    </script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Select Year(s)",
                allowClear: true
            });
        });
    </script>
@endsection
