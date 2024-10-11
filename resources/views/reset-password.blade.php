<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Update Password</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{url('assets/plugins/fontawesome-free/css/all.min.css')}}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="{{url('assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{url('assets/plugins/dist/css/adminlte.min.css')}}">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <style>
        #toggle-password {
            border-left: none;
            border-color: #ced4da;
        }

        #toggle-password:focus {
            border-left: none;
            border-color: #ced4da !important;
        }
    </style>
    <script>
        setTimeout(function () {
            $('.alert').fadeOut();
        },3000)
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const passwordField = document.getElementById('password-field');
            const togglePassword = document.getElementById('toggle-password');
            const toggleIcon = document.getElementById('toggle-icon');

            togglePassword.addEventListener('click', function () {
                // Toggle the input type
                const type = passwordField.type === 'password' ? 'text' : 'password';
                passwordField.type = type;

                // Toggle the icon
                const iconClass = type === 'password' ? 'fa-eye' : 'fa-eye-slash';
                toggleIcon.classList.remove('fa-eye', 'fa-eye-slash');
                toggleIcon.classList.add(iconClass);
            });


            const passwordFields = document.getElementById('password-field-2');
            const togglePasswords = document.getElementById('toggle-password-2');
            const toggleIcons = document.getElementById('toggle-icon-2');

            togglePasswords.addEventListener('click', function () {
                // Toggle the input type
                const type = passwordFields.type === 'password' ? 'text' : 'password';
                passwordFields.type = type;

                // Toggle the icon
                const iconClass = type === 'password' ? 'fa-eye' : 'fa-eye-slash';
                toggleIcons.classList.remove('fa-eye', 'fa-eye-slash');
                toggleIcons.classList.add(iconClass);
            });
        });
    </script>

</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <span>Update Password</span>
    </div>
    <!-- /.login-logo -->
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Setup A New Password</p>
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <form action="{{ url('change-password') }}" method="POST">
                <input type="hidden" name="user_id" value="{{$userId}}">
                @csrf

                @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="input-group mb-3">
                    <input type="password" id="password-field" name="password" class="form-control" placeholder="Password" required tabindex="1">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" id="toggle-password">
                            <span class="fas fa-eye" id="toggle-icon"></span>
                        </button>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" id="password-field-2" name="confirm_password" class="form-control" placeholder="Confirm Password" required tabindex="2">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" id="toggle-password-2">
                            <span class="fas fa-eye" id="toggle-icon-2"></span>
                        </button>
                    </div>
                </div>
                <div class="row d-flex justify-content-center">
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block" tabindex="3">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="{{url('assets/plugins/jquery/jquery.min.js')}}"></script>
<!-- Bootstrap 4 -->
<script src="{{url('assets/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{url('assets/plugin/dist/js/adminlte.min.js')}}"></script>

</body>
</html>
