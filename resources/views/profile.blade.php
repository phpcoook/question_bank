@extends('layouts.layoutMaster')
@section('title',env('WEB_NAME').' | Update Profile')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Update Profile</h1>
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

        <section class="content m-2 ">
            <div class="card card-primary">
                <form id="student-create" action="{{url(Auth::user()->role.'/update/profile')}}" method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label" for="first_name">First Name</label>
                            <div class="controls">
                                <input type="text" name="first_name" value="{{Auth::user()->first_name}}"
                                       class="form-control">
                                @error('first_name')
                                <div class="text-danger">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="last_name">Last Name</label>
                            <div class="controls">
                                <input type="text" name="last_name" class="form-control"
                                       value="{{Auth::user()->last_name}}">
                                @error('last_name')
                                <div class="text-danger">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        @if(Auth::user()->role != 'admin')
                            <div class="form-group">
                                <label class="form-label" for="date_of_birth">Birth Date</label>
                                <div class="controls">
                                    <input type="date" id="txtDate" name="date_of_birth" class="form-control"
                                           value="{{Auth::user()->date_of_birth}}">
                                    @error('date_of_birth')
                                    <div class="text-danger">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        @endif
                        <div class="form-group">
                            <label class="form-label" for="field-3">Email</label>
                            <div class="controls">
                                <input type="text" {{(Auth::user()->role != 'admin')? 'readonly':''}} name="email"
                                       class="form-control" value="{{Auth::user()->email}}">
                                @error('email')
                                <div class="text-danger">{{ $message }}</div>@enderror
                            </div>
                        </div>

                    </div>

                    <div class="card-footer d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </section>
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Update Password</h1>
                    </div>
                </div>
            </div>
        </div>
        <section class="content m-2 mb-5">
            <div class="card card-primary">
                <form id="student-create" action="{{url('student/update/password')}}" method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label" for="old_password">Old Password</label>
                            <div class="controls">
                                <input type="password" name="old_password" class="form-control">
                                @error('old_password')
                                <div class="text-danger">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="new_password">New Password</label>
                            <div class="controls">
                                <input type="password" name="new_password" class="form-control">
                                @error('new_password')
                                <div class="text-danger">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="confirm_password">Confirm password</label>
                            <div class="controls">
                                <input type="password" name="confirm_password" class="form-control">
                                @error('confirm_password')
                                <div class="text-danger">{{ $message }}</div>@enderror
                            </div>
                        </div>

                    </div>

                    <div class="card-footer d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </section>
        @if(!empty($subscription))
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark">Subscription Detail</h1>
                        </div>
                    </div>
                </div>
            </div>
            <section class="content m-2 mb-5">
                <div class="card card-primary">
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label" for="old_password">Subscription Start : <span class="text-gray">{{$subscription['startDate']}}</span> </label>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="new_password">Subscription End :  <span class="text-gray">{{$subscription['endDate']}}</span></label>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="confirm_password">Your Subscription Renewal On :  <span class="text-gray">{{$subscription['renewalDate']}}</span></label>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-end">
                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#success_tic">
                            Cancel Subscription Renewal
                        </button>
                    </div>

                </div>
            </section>
        @endif
    </div>
@endsection

@section('page-script')
    <script>
        $(function () {
            var dtToday = new Date();

            var month = dtToday.getMonth() + 1;
            var day = dtToday.getDate();
            var year = dtToday.getFullYear();

            if (month < 10)
                month = '0' + month.toString();
            if (day < 10)
                day = '0' + day.toString();

            var maxDate = year + '-' + month + '-' + day;
            $('#txtDate').attr('max', maxDate);
        });
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
                    password: {
                        required: true,
                        minlength: 6
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
                    password: {
                        required: "Please provide a password",
                        minlength: "Password must be at least 6 characters long"
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
    <style>
        .modal-content {
            border-radius: 10px;
        }

        .success_tic .page-body {
            max-width: 370px;
            background-color: #FFFFFF;
            margin: 5% auto;
        }

        .success_tic .head {
            text-align: center;
            color: #ff182a;
            margin-bottom: 20px;
        }
        .success_tic .close {
            opacity: 1;
            position: absolute;
            right: 10px;
            top: 10px;
            font-size: 30px;
            color: #000;
        }

        .success_tic .checkmark-circle {
            width: 150px;
            height: 150px;
            position: relative;
            display: inline-block;
            vertical-align: top;
        }

        .success_tic .checkmark-circle .background {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: #fd7e14;
            position: absolute;
        }

        .success_tic .exclamation-mark {
            font-size: 100px; /* Adjust size as needed */
            color: #FFFFFF; /* Color of the exclamation mark */
            line-height: 150px; /* Center vertically within the circle */
            text-align: center; /* Center horizontally */
            position: absolute; /* Allow positioning within the circle */
            left: 0;
            right: 0;
            top: 0;
        }

        /* Remove checkmark styles */
        .success_tic .checkmark-circle .checkmark {
            display: none; /* Hide the checkmark if not needed */
        }

    </style>
    <div id="success_tic" class="modal fade success_tic" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="page-body text-center">
                    <div class="head">
                        <h3>Cancel Upcoming Renewal!</h3>
                    </div>
                    <h1>
                        <div class="checkmark-circle">
                            <div class="background"></div>
                            <div class="exclamation-mark">!</div>
                        </div>
                    </h1>
                    <br>
                    <p class="mb-0">Are you sure you want to cancel your subscription? Please note that after cancellation, you will no longer have access to our paid services once your plan ends.</p>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <a href="{{url('cancel-subscription')}}" class="btn btn-danger text-white">Cancel Renewal</a>
                </div>
            </div>
        </div>
    </div>

@endsection

