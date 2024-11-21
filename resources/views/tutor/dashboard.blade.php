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
                    {{--                    <div class="col-12 d-flex justify-content-end mb-2">--}}
                    {{--                        <a class="btn btn-primary" href="{{ route('create.tutor') }}">Add Tutor</a>--}}
                    {{--                    </div>--}}
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

        <div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Question Detail!</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="imageContainer" style="margin-top: 15px;"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="imageModalLabel">Image View</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">

                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
@section('page-script')
    <link rel="stylesheet" href="{{url('assets/plugins/toastr/toastr.css')}}">
    <script src="{{url('assets/plugins/toastr/toastr.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            var baseUrl = '{{url('/')}}'+'/';
            $('#question-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url:   '{{ route('question.data') }}',
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
    <script>
        $(document).on('click', '.view-details', function () {
            const rowId = $(this).data('id');
            $('#detailsModal').modal('show');
            $('#report_text').val('');
            $('#imageContainer').empty();

            $.ajax({
                url: "{{ route('question.details') }}",
                type: 'GET',
                data: { id: rowId },
                success: function (response) {
                    if (response.details) {
                        $('#report_text').val(response.details);
                    }

                    // Clear the container before appending new content
                    $('#imageContainer').empty();

                    if (response.images && response.images.length > 0) {
                        console.log('test----'+response.images.length);
                        response.images.forEach(function (image) {
                            {{--const baseUrl = '{{ url('/') }}';--}}
                            const imageUrl = `{{env('AJAX_URL')}}/storage/images/${image.image_name}`;

                            $('#imageContainer').append(
                                `<div style="display: inline-block; text-align: center; margin: 5px;">
                                     <img src="${imageUrl}" alt="${image.type}" class="img-thumbnail image-clickable" style="max-width: 100px; cursor: pointer;" data-image-url="${imageUrl}">
                                     <p>${image.type}</p>
                                </div>`
                            );
                        });

                        // Add click event for images to open modal
                        $('.image-clickable').on('click', function () {
                            const imageUrl = $(this).data('image-url');

                            // Hide the detailsModal first
                            $('#detailsModal').modal('hide');

                            // Wait for the modal to hide before showing the imageModal
                            $('#detailsModal').on('hidden.bs.modal', function () {
                                $('#imageModal .modal-body').html(
                                    `<img src="${imageUrl}" alt="Full View" class="img-fluid" style="width: 400px; height: 400px; object-fit: contain;">`
                                );
                                $('#imageModal').modal('show');
                                // Unbind the event to prevent multiple triggers
                                $('#detailsModal').off('hidden.bs.modal');
                            });
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching details:', error);
                }
            });
        });
    </script>
@endsection
