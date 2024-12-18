@extends('layouts.layoutMaster')
@section('title',env('WEB_NAME').' | Student List')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Student List</h1>
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
                <div class="row m-3">
                    <div class="col-12 d-flex justify-content-end mb-2">
                        <a class="btn btn-primary" href="{{ route('create.student') }}">Add student</a>
                    </div>
                    <div class="col-sm-12">
                        <table id="Student-table" class="table table-bordered table-hover dataTable" role="grid"
                               aria-describedby="example2_info">
                            <thead>
                            <tr role="row">
                                <th>No</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Year</th>
                                <th>Birth of date</th>
                                <th>Subscription</th>
                                <th>Actions</th> <!-- Add Actions column header -->
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('page-script')
    <script>
        $(document).ready(function () {
            $('#Student-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{env('AJAX_URL')}}' + '/student/data',
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'first_name', name: 'first_name'},
                    {data: 'last_name', name: 'last_name'},
                    {data: 'email', name: 'email'},
                    {data: 'std', name: 'std'},
                    {data: 'date_of_birth', name: 'date_of_birth'},
                    {
                        data: 'subscription',
                        name: 'subscription',
                        orderable: false,
                        searchable: false,
                        class: 'font-size-smaller',
                    },
                    {data: 'actions', name: 'actions', orderable: false, searchable: false}

                ]
            });

            // Handle delete button click
            $('#Student-table').on('click', '.delete-student', function () {
                var id = $(this).data('id');
                if (confirm('Are you sure you want to delete this item?')) {
                    $.ajax({
                        url: '{{env('AJAX_URL')}}' + '/student/' + id,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (result) {
                            $('#Student-table').DataTable().ajax.reload();
                        },
                        error: function (error) {
                            toastr.error('Unable to delete post due to foreign key constraint');
                        }
                    });
                }
            });
        });
        $('#Student-table').on('draw.dt', function () {
            $('.custom-control-input').on('change', function (event, state) {
                var id = $(this).data('id');
                $.ajax({
                    url: '{{env('AJAX_URL')}}' + '/student/subscription/' + id,
                    type: 'get',
                    success: function (result) {
                        toastr.success('Subscription has been Updated!')
                    },
                    error: function (error) {
                        toastr.error('Unable to update. Something Went Wrong');
                    }
                });
            });
        });

    </script>
    <style>
        .custom-switch .custom-control-label::before {
            left: -3.25rem;
            width: 2.75rem;
            pointer-events: all;
            border-radius: 13px;
            height: 24px;
        }

        .custom-switch .custom-control-label::after {
            top: calc(.25rem + 2px);
            left: -49px; /* Position when switch is off */
            width: 20px;
            height: 20px;
            background-color: #adb5bd;
            border-radius: 10px;
            transition: transform .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        }

        .custom-switch .custom-control-input:checked + .custom-control-label::after {
            left: -41px; /* Position when switch is on */
        }

    </style>

    <script src="{{url('assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js')}}"></script>


@endsection
