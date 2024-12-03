@extends('layouts.layoutMaster')
@section('title',env('WEB_NAME').' | Wrong Question List')
@section('page-style')
    <style>
        .input_image_div {
            max-width: 12rem;
            margin-right: 12px;
        }
        .input_image {
            max-width: 100%;
        }
    </style>
@endsection
@section('content')
    <div class="popoverlay" id="popoverlay">
        <span class="popclose" id="popcloseBtn">&times;</span>
        <img src="" id="popupImage" alt="Popup Image">
    </div>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Wrong Question List</h1>
                    </div>
                    <div class="col-sm-6 text-right">
                        <select id="quiz_id" class="form-control" required>
                            <option value="">Filter By Quiz</option>
                            @foreach($all as $single)
                            <option value="{{$single->quiz_id}}" >{{$single->quiz_id}}</option>
                            @endforeach
                        </select>
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
            <div class="alert alert-danger alert-dismissible mx-3">
                <div class="d-flex gap-2">
                    <h5><i class="icon fas fa-ban"></i></h5>
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                </div>
            </div>
        @endif

        <section class="content m-2">
            <div class="card card-primary p-4">
                <div class="col-sm-12" id="byQuiz">
                    @if($questions->isNotEmpty())
                        @foreach ($questions as $question)
                            <div class="question mb-4">
                                <h4>Question Code: {{ $question->code }}</h4>
                                <p>Difficulty: {{ $question->difficulty }}</p>
                                <p>Time: {{ $question->time / 60 }} minute</p>
                                <h5>Images:</h5>
                                <div class="images d-flex flex-wrap">
                                    @if($question->quizImage->isNotEmpty())
                                        @foreach ($question->quizImage as $image)
                                            <div class="input_image_div">
                                                <img src="{{ asset('storage/images/' . $image->image_name) }}" alt="image"
                                                     class="input_image popthumb">
                                            </div>
                                        @endforeach
                                    @else
                                        <p>No images available for this question.</p>
                                    @endif
                                </div>
                            </div>
                            <hr>
                        @endforeach

                        <div class="d-flex justify-content-center">
                        {{ $questions->links() }}
                        </div>
                    @else
                        <div class="d-flex justify-content-center">
                            <h4>No Wrong questions found.</h4>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>
@endsection
@section('page-script')
    <script>
        $(window).on('load', function() {
            var url = window.location.href;
            var idPart = url.split('/').pop();
            if (idPart !== 'question' && !isNaN(idPart)) {
                $('#quiz_id').val(idPart);
            }
        });

        $('#quiz_id').on('change', function () {
            var selectedValue = this.value;
            if (selectedValue) {
                window.location.href = '{{ url('student/wrong/question') }}' + '/' + selectedValue;
            } else {
                window.location.href = '{{ url('student/wrong/question') }}';
            }
        });
    </script>
@endsection
