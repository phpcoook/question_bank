@extends('layouts.layoutMaster')
@section('title',env('WEB_NAME').' | Question Details')
@section('page-style')
    <style>
        .question-details {
            display: grid;
            width: 100%;
            gap: 30px;
        }

        .question-details img {
            margin: 0 auto;
            width: 70%;
            border: 1px solid #ddd;
            border-radius: 5px;
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
                        <h1 class="m-0 text-dark">Question Details</h1>
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
            <div class="card card-primary p-5 question-details">
                @php
                    $groupedImages = collect($images)->groupBy('type');
                @endphp

                @foreach($groupedImages as $type => $group)
                    <div class="d-flex justify-content-center flex-column">
                        <h3 class="text-center mb-4">{{ ucfirst($type) }} Image</h3>
                        @foreach($group as $image)
                            <img src="{{ asset('storage/images/' . $image['image_name']) }}" alt="{{ $type }}" class="img-fluid pb-4 popthumb">
                        @endforeach
                    </div>
                @endforeach
            </div>
        </section>
    </div>
@endsection
@section('page-script')
    <link rel="stylesheet" href="{{url('assets/plugins/toastr/toastr.css')}}">
    <script src="{{url('assets/plugins/toastr/toastr.min.js')}}"></script>
@endsection
