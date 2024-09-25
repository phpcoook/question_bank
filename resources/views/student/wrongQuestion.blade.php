@extends('layouts.layoutMaster')
@section('title',env('WEB_NAME').' | Wrong Question List')
@section('page-style')
    <style>
        .input_image_div {
            max-width: 12rem;
            margin-right: 12px;
        }
        .input_image {
            max-width: -webkit-fill-available;
        }
    </style>
@endsection
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Wrong Question List</h1>
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
            <div class="card card-primary p-4">
                <div class="col-sm-12">
                    @if(!empty($questions))
                        @foreach ($questions as $question)
                            <div class="question">
                                <h4>Question Code: {{ $question['code'] }}</h4>
                                <p>Difficulty: {{ $question['difficulty'] }}</p>
                                <p>Time: {{ $question['time'] }} minute</p>
                                <h5>Images:</h5>
                                <div class="images d-flex flex-wrap ">

                                    @foreach ($question['quiz_image'] as $image)
                                        <div class="input_image_div">
                                            <img src="{{ asset('storage/images/' . $image['image_name']) }}" alt="image"
                                                 class="input_image">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <hr>
                        @endforeach
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
