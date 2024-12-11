@extends('layouts.layoutMaster')
@section('title',env('WEB_NAME').' | Question Details')
@section('page-style')
    <style>
        .question-details {
            display: flex;
            flex-direction: column;
            gap: 2rem; /* Adds spacing between each section (Question, Solution, Answer) */
        }

        .question-details div {
            display: grid;
            grid-template-columns: repeat(4, 1fr); /* Maximum 4 images in one row */
            gap: 1rem; /* Space between images */
            justify-items: center; /* Centers images horizontally */
            align-items: center; /* Centers images vertically */
        }

        .popthumb {
            max-width: 100%;
            height: auto;
            border: 1px solid #ddd; /* Adds a border around the images */
            border-radius: 5px; /* Slight rounding of the edges */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Adds a subtle shadow */
            transition: transform 0.3s ease, box-shadow 0.3s ease; /* Smooth transition for hover effect */
        }

        .popthumb:hover {
            transform: scale(1.05); /* Slight zoom effect on hover */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); /* Emphasized shadow on hover */
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
                    <h3 class="text-center mb-4">{{ ucfirst($type) }} Image</h3>
                    <div class="">
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
