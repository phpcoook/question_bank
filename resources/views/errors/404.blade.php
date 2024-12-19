@extends('layouts.layoutBlank')
@section('title',env('WEB_NAME').' | 404 List')
@section('content')
    <div class="content-wrappers">
        <section class="content" style="margin-top: 100px">
            <div class="error-page">
                <div class="">
                    <h2 class="headline text-primary" style="font-size: 72px"> 404</h2>
                    <h3><i class="fas fa-exclamation-triangle text-primary"></i> Oops! Page not found.</h3>
                    <p>
                        We could not find the page you were looking for. Meanwhile, you may <a href="{{url('/')}}">return back</a>.
                    </p>
                </div>
                <!-- /.error-content -->
            </div>
            <!-- /.error-page -->
        </section>
        <!-- /.content -->
    </div>
@endsection
