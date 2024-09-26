@extends('layouts.layoutMaster')
@section('title',env('WEB_NAME').' | Student Edit')
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
                        required: "Please select the Standard"
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
@endsection
