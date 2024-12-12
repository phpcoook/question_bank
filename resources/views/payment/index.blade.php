@extends('layouts.layoutMaster')
@section('title',env('WEB_NAME').' | Payment History List')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="row mb-2 p-2">
                <div class="col-sm-12 col-md-4">
                    <h1 class="m-0 text-dark">Payment History List</h1>
                </div>
                @if(auth()->user()->role == 'admin')
                    <div class="col-sm-12 col-md-4 form-group">
                        <select id="user" class="text-left form-control select2">
                            <option value="">Filter By User</option>
                            @if(!empty($users->user))
                            @foreach($users as $user)
                                <option value="{{$user->user->id}}">{{$user->user->email}}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-sm-12 col-md-4 text-right">
                        <select id="status" class="form-control">
                            <option value="">Filter By Status</option>
                            <option value="active" class="text-success">Active</option>
                            <option value="expired" class="text-danger">Expired</option>
                        </select>
                    </div>
                @endif
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
                    <div class="col-sm-12">
                        <table id="Subscriber-table" class="table table-bordered table-hover dataTable" role="grid"
                               aria-describedby="example2_info">
                            <thead>
                            <tr role="row">
                                <th>No</th>
                                <th>Subscriber</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                                @if(auth()->user()->role == 'admin')
                                    <th>Stripe Subscription Id</th>
                                @endif
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
    <link rel="stylesheet" href="{{url('assets/plugins/select2/css/select2.css')}}">
    <script src="{{url('assets/plugins/select2/js/select2.full.js')}}"></script>
    <script>
        $(document).ready(function () {
            $('.select2').select2()
            $('#Subscriber-table').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 10,
                allowHTML: true,
                ajax: {
                    url: '{{env('AJAX_URL')}}' + '/payment_history',
                    data: function (d) {
                        d.filter = $('#status').val();
                        d.user = ('{{auth()->user()->role == 'admin'}}')? $('#user').val():0
                    }
                },
                columns: [
                    {data: 'no', searchable: false},
                    {data: 'subscriber'},
                    {data: 'plan_start_date'},
                    {data: 'plan_end_date'},
                    {data: 'amount'},
                    {data: 'status'},
                        @if(auth()->user()->role == 'admin')
                    {
                        data: 'stripe_id', name: 'stripe_id'
                    }
                    @endif
                ],
                "initComplete": function (settings, json) {
                    $('.show-response').on('click', function (event) {
                        const dataId = $(this).data('id');
                        const decodedValue = atob(dataId);
                        $('#modal-body').html(decodedValue);
                        $('#ultraModal-8').modal('show');
                    });
                }
            });
            $('#status').on('change', function () {
                $('#Subscriber-table').DataTable().draw();
            });
            $('#user').on('change', function () {
                $('#Subscriber-table').DataTable().draw();
            });
            // Handle delete button click
            $('#Subscriber-table').on('click', '.delete-student', function () {
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
                            alert('Unable to delete post due to foreign key constraint');
                        }
                    });
                }
            });
        });

        $(document).ready(function () {
            $('.show-response').on('click', function (event) {
                const dataId = $(this).data('id');
                const decodedValue = atob(dataId);
                $('#modal-body').text(decodedValue);
                $('#ultraModal-8').modal('show');
            });
        });

    </script>

    <div class="modal fade" id="ultraModal-8">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title">Select Notification Group</h4>
                </div>
                <form id="selection_form">
                    <div id="modal-body" class="modal-body">
                        Content is loading...
                    </div>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <style>
        .select2-container--default .select2-selection--single {
            padding: 5px;
            height: 35px;
            margin-top: 2px;
            border: 1px solid #ced4da;
        }
    </style>
@endsection
