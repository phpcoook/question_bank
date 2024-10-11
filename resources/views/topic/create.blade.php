@extends('layouts.layoutMaster')
@section('title',env('WEB_NAME').' | Topic Create')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Add Topic</h1>
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
                <form id="topic-form" action="{{route('topic.story')}}" method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="std">Year</label>
                            <select name="std" class="form-control" required>
                                <option value="">Select Year</option>
                                <option value="12" {{ (old('std') == 12) ? 'selected' : '' }}>12<sup>th</sup></option>
                                <option value="11" {{ (old('std') == 11) ? 'selected' : '' }}>11<sup>th</sup></option>
                                <option value="10" {{ (old('std') == 10) ? 'selected' : '' }}>10<sup>th</sup></option>
                                <option value="9" {{ (old('std') == 9) ? 'selected' : '' }}>9<sup>th</sup></option>
                                <option value="8" {{ (old('std') == 8) ? 'selected' : '' }}>8<sup>th</sup></option>
                                <option value="7" {{ (old('std') == 7) ? 'selected' : '' }}>7<sup>th</sup></option>
                                <option value="6" {{ (old('std') == 6) ? 'selected' : '' }}>6<sup>th</sup></option>
                                <option value="5" {{ (old('std') == 5) ? 'selected' : '' }}>5<sup>th</sup></option>
                                <option value="4" {{ (old('std') == 4) ? 'selected' : '' }}>4<sup>th</sup></option>
                                <option value="3" {{ (old('std') == 3) ? 'selected' : '' }}>3<sup>rd</sup></option>
                                <option value="2" {{ (old('std') == 2) ? 'selected' : '' }}>2<sup>nd</sup></option>
                                <option value="1" {{ (old('std') == 1) ? 'selected' : '' }}>1<sup>st</sup></option>
                            </select>
                            @error('std')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" name="title" id="title" class="form-control"
                                   placeholder="Enter Title" value="{{ old('title') }}" required>
                            @error('title')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection

@section('page-script')
    <script>
        $(document).ready(function () {
            $("#topic-form").validate({
                rules: {
                    std: {
                        required: true
                    },
                    title: {
                        required: true,
                    },
                },
                messages: {
                    std: {
                        required: "Please select the Year"
                    },
                    title: {
                        required: "Please enter a Title"
                    },
                },
                errorElement: 'div',
                errorPlacement: function (error, element) {
                    error.addClass('text-danger');
                    element.closest('.form-group').append(error);
                },
                highlight: function (element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function (element) {
                    $(element).removeClass('is-invalid');
                }
            });
        });
    </script>
@endsection
