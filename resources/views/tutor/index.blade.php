@extends('layouts.layoutMaster')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Tutor List</h1>
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
                        <a class="btn btn-primary" href="{{ route('create.tutor') }}">Add Tutor</a>
                    </div>
                    <div class="col-sm-12">
                        <table id="Tutor-table" class="table table-bordered table-hover dataTable" role="grid"
                               aria-describedby="example2_info">
                            <thead>
                            <tr role="row">
                                <th>No</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Birth of date</th>
                                <th>Actions</th>
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
            $('#Tutor-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('tutor.data') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'first_name', name: 'first_name'},
                    {data: 'last_name', name: 'last_name'},
                    {data: 'email', name: 'email'},
                    {data: 'date_of_birth', name: 'date_of_birth'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false}
                ]
            });

            // Handle delete button click
            $('#Tutor-table').on('click', '.delete-tutor', function () {
                var id = $(this).data('id');
                if (confirm('Are you sure you want to delete this item?')) {
                    $.ajax({
                        url: '/tutor/' + id,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (result) {
                            $('#Tutor-table').DataTable().ajax.reload();
                            alert('Item deleted successfully!');
                        },
                        error: function (error) {
                            alert('Error deleting item!');
                        }
                    });
                }
            });
        });
    </script>
@endsection
