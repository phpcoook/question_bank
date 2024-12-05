@extends('layouts.layoutMaster')
@section('title',env('WEB_NAME').' | Tutor Dashboard')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Dashboard</h1>
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
                        <table id="question-table" class="table table-bordered table-hover dataTable">
                            <thead>
                            <tr role="row">
                                <th>No</th>
                                <th>Code</th>
                                <th>Difficulty</th>
                                <th>Time</th>
                                <th>Topic</th>
                                <th>SubTopic</th>
                                <th>Std</th>
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
    <link rel="stylesheet" href="{{url('assets/plugins/toastr/toastr.css')}}">
    <script src="{{url('assets/plugins/toastr/toastr.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            $('#question-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{env('AJAX_URL')}}' + '/question/data',
                    type: "POST",
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'code', name: 'code'},
                    {data: 'difficulty', name: 'difficulty', searchable: false},
                    {data: 'time', name: 'time', searchable: false},
                    {data: 'topic_id', name: 'topic_id', searchable: false},
                    {data: 'subtopic_id', name: 'subtopic_id', searchable: false},
                    {data: 'std', name: 'std', searchable: false},
                    {data: 'actions', name: 'actions', searchable: false},
                ]
            });
        });
    </script>
@endsection
