@extends('layouts.layoutMaster')
@section('page-style')
    <style>
        .ellipsis {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .question-col, .answer-col {
            max-width: 200px;
        }
    </style>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Question List</h1>
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
                        <a class="btn btn-primary" href="{{ route('create.question') }}">Add Question</a>
                    </div>
                    <div class="col-sm-12">
                        <table id="Question-table" class="table table-bordered table-hover dataTable" role="grid"
                               aria-describedby="example2_info">
                            <thead>
                            <tr role="row">
                                <th>No</th>
                                <th>Code</th>
                                <th>Difficulty</th>
                                <th>Question</th>
                                <th>Answer</th>
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
            $('#Question-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('questions.data') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'code', name: 'code'},
                    {data: 'difficulty', name: 'difficulty'},
                    {
                        data: 'question',
                        name: 'question',
                        render: function (data, type, row) {
                            return '<div class="ellipsis question-col" title="' + data + '">' + data + '</div>';
                        }
                    },
                    {
                        data: 'answer',
                        name: 'answer',
                        render: function (data, type, row) {
                            return '<div class="ellipsis answer-col" title="' + data + '">' + data + '</div>';
                        }
                    },
                    {data: 'actions', name: 'actions', orderable: false, searchable: false}
                ]
            });

            // Handle delete button click
            $('#Question-table').on('click', '.delete-question', function () {
                var id = $(this).data('id');
                if (confirm('Are you sure you want to delete this item?')) {
                    $.ajax({
                        url: '/question/' + id,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (result) {
                            $('#Question-table').DataTable().ajax.reload(); // Reload table data
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
