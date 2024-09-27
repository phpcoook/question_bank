@extends('layouts.layoutMaster')
@section('title',env('WEB_NAME').' | Student Dashboard')
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
                    <div class="col-md-6 m-auto">
                        <p class="text-center">
                            <strong>Covered Topic</strong>
                        </p>
                        @foreach($topicData as $topicItem)
                            <div class="progress-group">
                                {{$topicItem['title']}}
                                <span class="float-right"><b>{{$topicItem['attempted_questions']}}</b>/{{$topicItem['total_questions']}}</span>
                                <div class="progress progress-md">
                                    <div class="progress-bar bg-success" style="width:{{($topicItem['attempted_questions']/$topicItem['total_questions'])*100}}%"></div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

