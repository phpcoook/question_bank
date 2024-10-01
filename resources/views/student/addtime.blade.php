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
                        <h1 class="m-0 text-dark">Select Time</h1>
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
                            <select name="topics[]" id="topics" class="form-control select2" multiple required>
                                <option value="">Select Topics</option>
                                @foreach($topics as $topic)
                                    <option {{(old('topics'))? in_array($topic->id, old('topics'))?'selected':'' : ''}} value="{{$topic->id}}">{{$topic->title}}</option>
                                @endforeach
                            </select>
                            @error('sub_topics')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="sub_topics">Sub Topics</label>
                            <select name="sub_topics[]" id="sub_topics" class="form-control select3" multiple required>
                                <option value="">Select Sub Topics</option>
                            </select>
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

                        <div class="form-group">
                            <label for="difficulty">Difficulty</label>
                            <select name="difficulty" class="form-control" required>
                                <option value="">Select Difficulty</option>
                                <option value="foundation" {{ old('difficulty') == 'foundation' ? 'selected' : '' }}>
                                    Foundation
                                </option>
                                <option
                                    value="intermediate" {{ old('difficulty') == 'intermediate' ? 'selected' : '' }}>
                                    Intermediate
                                </option>
                                <option value="challenging" {{ old('difficulty') == 'challenging' ? 'selected' : '' }}>
                                    Challenging
                                </option>
                            </select>
                            @error('difficulty')
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
                    url: '{{env('AJAX_URL')}}'+'getSubTopicData',
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
            // Handle adding new image input fields for question images

            $('#topics').change(function () {
                const selectedOptions = $(this).val(); // This should be an array
                $.ajax({
                    url: '{{env('AJAX_URL')}}'+'getSubTopicData',
                    type: 'POST',
                    data: {
                        'topic_ids': selectedOptions, // Send as array
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
            });
        });
    </script>
@endsection
