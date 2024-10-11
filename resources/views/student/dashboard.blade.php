@extends('layouts.layoutMaster')
@section('title',env('WEB_NAME').' | Student Dashboard')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Student Dashboard</h1>
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
            <div class="card card-primary ">
                <div class="row m-3">
                    <div class="col-md-{{empty($subscription)?'12':'12 d-none'}}">
                        <h4>Subscription Plan</h4>
                        <div class="card-deck mb-3 text-center">
                            <div class="card mb-4 box-shadow">
                                <div class="card-header">
                                    <h4 class="my-0 font-weight-normal">Free PLan</h4>
                                </div>
                                <div class="card-body pricing">
                                    <h1 class="card-title pricing-card-title w-100 text-bold">$0</h1>
                                    <br>
                                    <ul>
                                        <li><i class="fa fa-check text-success"></i> One {{$setting->no_of_questions}}
                                            minute exam once a week
                                        </li>
                                        <li><i class="fa fa-times text-danger"></i> Unable to see previous correct
                                            answers
                                        </li>
                                        <li><i class="fa fa-times text-danger"></i> Unable to look at full working out
                                            for questions
                                        </li>
                                        <li><i class="fa fa-times text-danger"></i> Solution not displayed during the test
                                        </li>
                                    </ul>
                                    <button disabled type="button" class="btn btn-lg btn-block btn-primary">
                                        Purchased
                                    </button>
                                </div>
                            </div>
                            <div class="card mb-4 box-shadow">
                                <div class="card-header">
                                    <h4 class="my-0 font-weight-normal">Paid Plan</h4>

                                </div>
                                <div class="card-body pricing">
                                    <h1 class="card-title pricing-card-title w-100 text-bold">
                                        ${{ ucfirst($setting->subscription_charge) }}</h1>
                                    <br>
                                    <ul>
                                        <li><i class="fa fa-check text-success"></i> Unlimited Exam Generations that are
                                            all {{$setting->no_of_questions}} minute long
                                        </li>
                                        <li><i class="fa fa-check text-success"></i> Able to see previous correct
                                            answers
                                        </li>
                                        <li><i class="fa fa-check text-success"></i> Able to look at full working out
                                            for questions
                                        </li>
                                        <li><i class="fa fa-check text-success"></i> Recurring subscription via Stripe
                                        </li>
                                    </ul>
                                    <button type="button"
                                            {{ !empty($subscription) ? 'disabled':0 }}
                                            {{ $setting->subscription_charge ? "onclick=payMethod('".$setting->subscription_charge."')" : 'disabled' }}
                                            class="btn btn-lg btn-block btn-primary">
                                        {{ !empty($subscription) ? 'Plan Purchased':'Buy' }}
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="card card-primary mt-3 mb-5">
                <div class="row m-3">

                    <div class="col-md-6 m-auto">
                        <h4 class="mb-1">Questions done / Topic</h4>
                        @if(!empty($subscription))
                            <p class="mb-3" ><strong>Your Plan Renewal On
                                    : </strong> {{ !empty($subscription) ? date('d-m-Y',strtotime($subscription->end_date)):'' }}
                            </p>
                            @if(!$subscriptionStatus)
                                <p class="mb-3 text-danger" ><strong>Your Subscription Disable by Owner</strong></p>
                            @endif
                        @endif
                        <div class="card p-3 box-shadow">
                            @foreach($topicData as $topicItem)
                                <div class="progress-group">
                                    {{$topicItem['title']}}
                                    <span class="float-right">
                                    <b>{{$topicItem['attempted_questions']}}</b>/
                                        {{$topicItem['total_questions']}}
                                </span>
                                    <div class="progress progress-md">
                                        @if($topicItem['total_questions'] > 0)
                                            <div class="progress-bar bg-success"
                                                 style="width:{{ ($topicItem['attempted_questions'] / $topicItem['total_questions']) * 100 }}%">
                                            </div>
                                        @else
                                            <div class="progress-bar bg-success" style="width:0%">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                </div>
            </div>

        </section>
    </div>

@endsection
@section('page-script')
    <style>
        /* Style for the payment modal */
        #paymentModal {
            padding: 20px;
        }

        /* Card element container */
        #card-element {
            padding: 10px;
            border: 1px solid #ced4da; /* Light gray border */
            border-radius: 5px; /* Rounded corners */
            background-color: #f8f9fa; /* Light background */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Subtle shadow */
            transition: border-color 0.2s; /* Smooth transition for border color */
            margin: 10px auto;
        }

        #card-element div {
            width: 300px;
            height: 50px;
            margin: 0 auto;

        }

        /* Focus state for card element */
        #card-element:focus {
            border-color: #007bff; /* Bootstrap primary color */
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5); /* Light blue glow */
        }

        /* Button styles */
        #submitPay {
            width: 100%; /* Full width */
            padding: 10px; /* Padding for the button */
            background-color: #007bff; /* Bootstrap primary color */
            color: #fff; /* White text */
            border: none; /* No border */
            border-radius: 5px; /* Rounded corners */
            font-size: 16px; /* Font size */
            transition: background-color 0.2s; /* Smooth transition for background color */
        }

        #submitPay:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }

        #payment-result {
            margin-top: 15px; /* Space above result message */
            font-weight: bold; /* Bold text for messages */
        }

        /*success model*/
        #success_tic .page-body {
            max-width: 300px;
            background-color: #FFFFFF;
            margin: 10% auto;
        }

        #success_tic .page-body .head {
            text-align: center;
        }

        #success_tic .close {
            opacity: 1;
            position: absolute;
            right: 0px;
            font-size: 30px;
            padding: 3px 15px;
            margin-bottom: 10px;
        }

        #success_tic .checkmark-circle {
            width: 150px;
            height: 150px;
            position: relative;
            display: inline-block;
            vertical-align: top;
        }

        .checkmark-circle .background {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: #1ab394;
            position: absolute;
        }

        #success_tic .checkmark-circle .checkmark {
            border-radius: 5px;
        }

        #success_tic .checkmark-circle .checkmark.draw:after {
            -webkit-animation-delay: 300ms;
            -moz-animation-delay: 300ms;
            animation-delay: 300ms;
            -webkit-animation-duration: 1s;
            -moz-animation-duration: 1s;
            animation-duration: 1s;
            -webkit-animation-timing-function: ease;
            -moz-animation-timing-function: ease;
            animation-timing-function: ease;
            -webkit-animation-name: checkmark;
            -moz-animation-name: checkmark;
            animation-name: checkmark;
            -webkit-transform: scaleX(-1) rotate(135deg);
            -moz-transform: scaleX(-1) rotate(135deg);
            -ms-transform: scaleX(-1) rotate(135deg);
            -o-transform: scaleX(-1) rotate(135deg);
            transform: scaleX(-1) rotate(135deg);
            -webkit-animation-fill-mode: forwards;
            -moz-animation-fill-mode: forwards;
            animation-fill-mode: forwards;
        }

        #success_tic .checkmark-circle .checkmark:after {
            opacity: 1;
            height: 75px;
            width: 37.5px;
            -webkit-transform-origin: left top;
            -moz-transform-origin: left top;
            -ms-transform-origin: left top;
            -o-transform-origin: left top;
            transform-origin: left top;
            border-right: 15px solid #fff;
            border-top: 15px solid #fff;
            border-radius: 2.5px !important;
            content: '';
            left: 35px;
            top: 80px;
            position: absolute;
        }

        @-webkit-keyframes checkmark {
            0% {
                height: 0;
                width: 0;
                opacity: 1;
            }
            20% {
                height: 0;
                width: 37.5px;
                opacity: 1;
            }
            40% {
                height: 75px;
                width: 37.5px;
                opacity: 1;
            }
            100% {
                height: 75px;
                width: 37.5px;
                opacity: 1;
            }
        }

        @-moz-keyframes checkmark {
            0% {
                height: 0;
                width: 0;
                opacity: 1;
            }
            20% {
                height: 0;
                width: 37.5px;
                opacity: 1;
            }
            40% {
                height: 75px;
                width: 37.5px;
                opacity: 1;
            }
            100% {
                height: 75px;
                width: 37.5px;
                opacity: 1;
            }
        }

        @keyframes checkmark {
            0% {
                height: 0;
                width: 0;
                opacity: 1;
            }
            20% {
                height: 0;
                width: 37.5px;
                opacity: 1;
            }
            40% {
                height: 75px;
                width: 37.5px;
                opacity: 1;
            }
            100% {
                height: 75px;
                width: 37.5px;
                opacity: 1;
            }
        }


    </style>
    @if(auth()->check() && auth()->user()->role == 'student')


        <script src="https://js.stripe.com/v3/"></script>
        <script>
            const appearance = {
                theme: 'flat'
            };
            const stripe = Stripe('{{ env('STRIPE_KEY') }}');
            // const elements = stripe.elements();
            const elements = stripe.elements({stripe, appearance});
            let cardElement;

            function payMethod(payId) {
                $("#priceModal").modal('hide');
                $("#paymentModal").modal('show');
                initializeCardElement();
            }

            function initializeCardElement() {
                cardElement = elements.create('card');
                cardElement.mount('#card-element');
            }

            $(document).ready(function () {

                $("#submitPay").click(function() {
                    $(this).html(
                        `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>&nbsp;&nbsp; Processing...`
                    );
                });
                $('#submitPay').click(async (event) => {
                    // event.preventDefault();
                    const {paymentMethod, error} = await stripe.createPaymentMethod({
                        type: 'card',
                        card: cardElement,
                    });

                    if (error) {
                        $('#payment-result').text(error.message);
                    } else {
                        $.ajax({
                            url: '{{env('AJAX_URL')}}' +'payment',
                            type: 'POST',
                            data: {
                                payment_method_id: paymentMethod.id,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function (response) {
                                if (response.success) {
                                    $("#paymentModal").modal('hide');
                                    $("#success_tic").modal('show');
                                    setTimeout(function () {
                                        location.reload();
                                    },4000)
                                } else {
                                    $('#payment-result').text(response.message);
                                }
                            },
                            error: function (xhr) {
                                $('#payment-result').text(xhr.responseJSON.message || 'Payment failed.');
                            }
                        });
                    }
                });
            });
        </script>

        <!-- Pricing Modal -->
        <div id="priceModal" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Pricing</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="card-deck mb-3 text-center">
                            <div class="card mb-4 box-shadow">
                                <div class="card-header">
                                    <h4 class="my-0 font-weight-normal">Free PLan</h4>
                                </div>
                                <div class="card-body pricing">
                                    <h1 class="card-title pricing-card-title w-100 text-bold">$0</h1>
                                    <br>
                                    <ul>
                                        <li><i class="fa fa-check text-success"></i> One {{$setting->no_of_questions}}
                                            minute exam once a week
                                        </li>
                                        <li><i class="fa fa-times text-danger"></i> Unable to see previous correct
                                            answers
                                        </li>
                                        <li><i class="fa fa-times text-danger"></i> Unable to look at full working out
                                            for questions
                                        </li>
                                    </ul>
                                    <button disabled type="button" class="btn btn-lg btn-block btn-primary">
                                        Purchased
                                    </button>
                                </div>
                            </div>
                            <div class="card mb-4 box-shadow">
                                <div class="card-header">
                                    <h4 class="my-0 font-weight-normal">Paid Plan</h4>
                                </div>
                                <div class="card-body pricing">
                                    <h1 class="card-title pricing-card-title w-100 text-bold">
                                        ${{ ucfirst($setting->subscription_charge) }}</h1>
                                    <br>
                                    <ul>
                                        <li><i class="fa fa-check text-success"></i> Unlimited Exam Generations that are
                                            all {{$setting->no_of_questions}} minute long
                                        </li>
                                        <li><i class="fa fa-check text-success"></i> Able to see previous correct
                                            answers
                                        </li>
                                        <li><i class="fa fa-check text-success"></i> Able to look at full working out
                                            for questions
                                        </li>
                                        <li><i class="fa fa-check text-success"></i> Recurring subscription via Stripe
                                        </li>
                                    </ul>
                                    <button type="button"
                                            {{ $setting->subscription_charge ? "onclick=payMethod('".$setting->subscription_charge."')" : 'disabled' }}
                                            class="btn btn-lg btn-block btn-primary">
                                        Buy
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Modal -->
        <div id="paymentModal" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Payment</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="card-deck mb-3 text-center">
                            <div id="card-element"></div> <!-- Card input will be mounted here -->
                            <button id="submitPay" class="btn btn-primary mt-3">Pay</button>
                            <div id="payment-result" class="mt-3"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="success_tic" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <a class="close" href="#" data-dismiss="modal">&times;</a>
                    <div class="page-body page-body text-center">
                        <div class="head">
                            <h3 style="margin-top:5px;">Payment Done!</h3>
                        </div>
                        <h1 style="text-align:center;">
                            <div class="checkmark-circle">
                                <div class="background"></div>
                                <div class="checkmark draw"></div>
                            </div>
                        </h1>
                        <br>
                        <h4>Subscription successful.</h4>
                        <h4>Thank you!!</h4>
                    </div>
                </div>
            </div>

        </div>

    @endif


@endsection

