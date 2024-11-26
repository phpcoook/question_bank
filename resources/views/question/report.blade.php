@extends('layouts.layoutMaster')
@section('title',env('WEB_NAME').' | Report List')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Reported Question</h1>
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
                    <div class="col-sm-12">
                        <table id="reports-table" class="table table-bordered table-hover dataTable" role="grid"
                               aria-describedby="example2_info">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Report Text</th>
                                <th>Student Name</th>
                                <th>Question ID</th>
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
            $('#reports-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{env('AJAX_URL')}}' +'/questions/report',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'report_text', name: 'report_text' },
                    { data: 'user_name', name: 'user_name' },
                    { data: 'question_id', name: 'question_id' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false },
                ],
            });
        });

    </script>
@endsection
