@extends('layouts.layoutMaster')
@section('title',env('WEB_NAME').' | Subscriber List')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="row mb-2 p-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Subscriber List</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <select id="status" class="form-control" required>
                        <option value="">Filter By Status</option>
                        <option value="active" class="text-success">Active</option>
                        <option value="expired" class="text-danger">Expired</option>
                    </select>
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
                    <div class="col-sm-12">
                        <table id="Subscriber-table" class="table table-bordered table-hover dataTable" role="grid"
                               aria-describedby="example2_info">
                            <thead>
                                <tr role="row">
                                    <th>No</th>
                                    <th>Subscriber</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Status</th>
                                    <th>Paid</th>
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
            $('#Subscriber-table').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 10,
                allowHTML: true,
                ajax: {
                    url: '{{env('AJAX_URL')}}' + 'subscribers',
                    data: function (d) {
                        d.filter = $('#status').val();
                    }
                },
                columns: [
                    {data: 'no'},
                    {data: 'subscriber'},
                    {data: 'plan_start_date'},
                    {data: 'plan_end_date'},
                    {data: 'status'},
                    {data: 'amount'},
                ]
            });
            $('#status').on('change', function () {
                $('#Subscriber-table').DataTable().draw();
            });
            // Handle delete button click
            $('#Subscriber-table').on('click', '.delete-student', function () {
                var id = $(this).data('id');
                if (confirm('Are you sure you want to delete this item?')) {
                    $.ajax({
                        url: '{{env('AJAX_URL')}}' + 'student/' + id,
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
    </script>
@endsection
