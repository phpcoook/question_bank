@extends('layouts.layoutMaster')
@section('title',env('WEB_NAME').' | Add Quiz Time')
@section('page-style')
    <style>
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #007bff !important;
            border: 1px solid #007bff !important;
            color: #ffffff !important;
        }
    </style>
@endsection
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Quiz Generation</h1>
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

                <form id="time-add-form" action="{{route('student.start-quiz')}}" method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="sub_topics">Topics</label>
                            <div class="mx-2">
                                @foreach($allTopics as $year => $topics)
                                    <div class="form-check">
                                        <p class="m-1"><b>{{ str_replace('_', ' ', $year) }}</b></p>
                                        <div class="mx-5">
                                            @foreach($topics as $topic)
                                                <div>
                                                    <input type="checkbox" name="topics[]" class="form-check-input"
                                                           id="topic_{{$topic['id']}}"
                                                           value="{{$topic['id']}}" {{ (old('topics') && in_array($topic['id'], old('topics'))) ? 'checked' : '' }}>
                                                    <label class="form-check-label"
                                                           for="topic_{{$topic['id']}}">{{$topic['title']}}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('topics')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="sub_topics">Sub Topics</label>&nbsp;
                            <div id="sub_topics" class="mx-3"></div>
                            @error('sub_topics')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="time">Time</label>
                            <input type="number" max="{{$time->no_of_questions}}" name="time" id="time"
                                   class="form-control"
                                   placeholder="Enter Time" value="{{ old('time') }}" required>
                            @error('time')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-center mb-2">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>

            </div>
        </section>
    </div>
@endsection
@section('page-script')
    <link rel="stylesheet" href="{{url('assets/plugins/select2/css/select2.css')}}">
    <script src="{{url('assets/plugins/select2/js/select2.full.js')}}"></script>
    <script>
        $(document).ready(function () {

            $('.select2').select2();
        });
    </script>
    <script>
        $(document).ready(function () {
            if ($('#topics').val()) {
                $.ajax({
                    url: '{{env('AJAX_URL')}}' + '/getSubTopicData',
                    type: 'POST',
                    data: {
                        'topic_ids': $('#topics').val(), // Send as array
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (result) {
                        $('#sub_topics').html(result.data)
                        $('.select3').select2()
                    },
                    error: function (error) {
                        alert('Something Went Wrong!');
                    }
                });
            }
            $('.select2').select2()
            $('.select3').select2()

            $(document).on('change', 'input[name="topics[]"]', function () {
                let selectedTopics = $('input[name="topics[]"]:checked').map(function () {
                    return $(this).val();
                }).get();

                if (selectedTopics.length > 0) {
                    $.ajax({
                        url: '{{env('AJAX_URL')}}' + '/getSubTopicData',
                        type: 'POST',
                        data: {
                            'topic_ids': selectedTopics,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (result) {
                            $('#sub_topics').html(result.data);
                        },
                        error: function () {
                            alert('Something Went Wrong!');
                        }
                    });
                } else {
                    $('#sub_topics').html('<p>No SubTopics available.</p>');
                }
            });
        });
    </script>
@endsection
