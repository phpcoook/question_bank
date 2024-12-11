@extends('layouts.layoutMaster')
@section('title',env('WEB_NAME').' | Student Quiz')
@section('page-style')
    <style>
        .question {
            margin-bottom: 20px;
            font-size: 1.5rem;
        }

        .buttons {
            margin-top: 10px;
        }

        .timer {
            font-size: 1.2rem;
            margin-bottom: 15px;
            border: 3px solid #113581;
            border-radius: 10px;
            width: 94px;
            height: 45px;
            align-items: center;
            display: flex;
            gap: 5px;
            justify-content: center;
        }

        .extra-time {
            color: red;
        }

        .form-wizard {
            position: relative;
            display: table;
            margin: 0 auto;
            max-width: 540px;
        }

        .steps {
            margin: 40px 0;
            overflow: hidden;

        }

        .steps ul {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .steps ul li {
            float: left;
            color: #fff;
            padding: 0 15px;
            position: relative;
            cursor: pointer;
            -webkit-transition: all 0.4s ease-in-out 0;
            -moz-transition: all 0.4s ease-in-out 0;
            -ms-transition: all 0.4s ease-in-out 0;
            -otransition: all 0.4s ease-in-out 0;
            transition: all 0.4s ease-in-out 0;


        }

        .steps ul li:hover, .steps ul li.active {
            color: #007bff;
        }

        .steps ul li.active span {
            background: #007bff;
            color: #fff;
            border-radius: 17px;
            top: 3px;

        }

        .steps ul li.active::after {
            background: #007bff;
            width: 100%;
        }

        .steps ul li::before, .steps ul li::after {
            content: "";
            position: absolute;
            left: -50%;
            top: 22px;
            width: 100%;
            height: 3px;
            background: #F4F6F9;
            -webkit-transition: all 0.4s ease-in-out 0;
            -moz-transition: all 0.4s ease-in-out 0;
            -ms-transition: all 0.4s ease-in-out 0;
            -otransition: all 0.4s ease-in-out 0;
            transition: all 0.4s ease-in-out 0;
            -webkit-transform: translateY(-50%);
            -moz-transform: translateY(-50%);
            -ms-transform: translateY(-50%);
            -otransform: translateY(-50%);
            transform: translateY(-50%);
        }

        .steps ul li::after {
            width: 0;
        }

        .steps ul li span {

            display: block;
            margin: 0 auto 15px;
            width: 35px;
            height: 35px;
            text-align: center;
            background: #F4F6F9;
            font-size: 18px;
            line-height: 35px;
            font-weight: 300;
            color: #000;
            position: relative;
            z-index: 1;
            -webkit-transition: all 0.4s ease-in-out 0;
            -moz-transition: all 0.4s ease-in-out 0;
            -ms-transition: all 0.4s ease-in-out 0;
            -otransition: all 0.4s ease-in-out 0;
            transition: all 0.4s ease-in-out 0;
            -webkit-border-radius: 17px;
            -moz-border-radius: 17px;
            -ms-border-radius: 17px;
            -oborder-radius: 17px;
            border-radius: 17px;
            top: 3px;
        }

        .steps ul li:first-child::before, .steps ul li:first-child::after {
            display: none;
        }

        .displayNone {
            display: none;
        }

        .ans-correct {
            background: #C8E7A7;
            color: #5C9B56;
            border-radius: 10px;
        }

        .ans-wrong {
            background: #CA1E1E;
            color: red;
            border-radius: 10px;
        }

        .imgbox-bottom-btns {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .custom-progress-bar {
            display: grid;
            grid-template-columns: auto 1fr;
            align-items: center;
            gap: 20px;
        }

        .custom-progress-bar .progress {
            height: 5px;
            border-radius: 10px;
            background-color: #f2f2f2;
        }

        .custom-progress-bar .progress-bar {
            background-color: #113581;
        }

        .custom-progress-bar {
            margin: 40px 0;
        }

        .custom-progress-bar p {
            margin-bottom: 0;
            color: #666;
            font-size: 16px;
        }

        .question-code-box h6 {
            font-size: 20px;
            color: #000;
            font-weight: 600;
        }

        .question-code-box h3 {
            margin: 0 !important;
            font-size: 20px;
            color: #555;
        }

        .question-code-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 0 0 20px 0;
        }

        .correct-total {
            border: 3px solid #153883;
            border-radius: 50%;
            height: 150px;
            width: 150px;
            margin-right: auto;
            margin-left: auto;
        }

        .wrong-total {
            border: 3px solid #153883;
            border-radius: 50%;
            height: 150px;
            width: 150px;
            margin-right: auto;
            margin-left: auto;
        }

        .info-button {
            padding: 5px 80px !important;
            font-size: 13px;
            width: 100% !important;
        }

        .quiz-info-total {
            gap: 20px;
        }

        .dashboard-btn {
            background-color: #153883;
            color: #ffffff;
        }

        .dashboard-btn:hover {
            color: white;
        }

        .question-answer {
            margin-left: 5.5rem;
        }

        .solution-question {
            max-width: 100%;
        }

        .content .container {
            max-width: 1024px;
        }
    </style>
    <style>
        .question-box-progress {
            position: relative;
        }

        .progress-box {
            position: absolute;
            top: 45px;
            left: 10px;
        }

        .progress-box li {
            border: none !important;
        }

        .progress-box li:last-child {
            display: none;
        }

        span.progress-circle {
            width: 45px;
            height: 45px;
            border: 3px solid #ddd;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto;
            cursor: pointer;
        }

        .progress-circle p {
            margin: 0;
            font-size: 20px;
            color: #222;
            font-weight: 900;
        }

        .progress-line {
            border-left: 3px dashed #000;
            width: 5px;
            display: block;
            height: 30px;
            margin: 0 auto;
        }

        .progress-circle.answer-correct p {
            color: #28a745;
        }

        .progress-circle.answer-wrong p {
            color: #C10505;
        }

        .progress-circle.answer-correct {
            background-color: #c8e7a7;
            border-color: #28a745;
        }

        .progress-circle.answer-wrong {
            background-color: #f08d8d;
            border-color: #C10505;
        }
    </style>
    <style>
        .custom-btn-size {
            max-width: 170px;
            width: 170px;
        }
    </style>
    <style>
        /* Basic styling for the modal */
        #skipModal {
            display: none; /* Hidden by default */
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: rgba(0, 0, 0, 0.5);
            padding: 20px;
            z-index: 999;
            height: 100%;
            width: 100%;
        }
        .modal-content {
            background-color: white;
            padding: 20px;
            text-align: center;
        }
        .modal-buttons {
            margin-top: 20px;
        }
        .modal-buttons button {
            margin: 0 10px;
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
                        <h1 class="m-0 text-dark">Start Quiz</h1>
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
            <div class="card card-primary text-center question-box-progress">
                <div class="container">
                    @if(!empty($randomCombination))
                        <div class="form-wizard" style="display: none;">
                            <div class="steps">
                                <ul id="li-steps">
                                    @foreach($randomCombination as $i=>$question)
                                        <li>
                                            <span>{{$i+1}}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <div class="custom-progress-bar" id="custom-progress-bar">
                            <p><span id="try-solution">1</span> of
                                <span id="total-question">{{ count($randomCombination) }}</span>
                            </p>
                            <div class="progress">
                                <div class="progress-bar" id="progress-bar" role="progressbar" aria-valuenow="75"
                                     aria-valuemin="0"
                                     aria-valuemax="100"
                                     style="width: 0%;"
                                ></div>
                            </div>
                        </div>
                        <div class="question-code-box" id="question-code-box">
                            <h6>
                                Question – <span id="question-code"></span><br>
                            </h6>

                            <h3>Difficulty – <span id="question-difficulty"></span></h3>

                            <div class="d-flex justify-content-end">
                                <div class="timer" id="timer">
                                    <svg fill="#000000" height="30px" width="30px" version="1.1" id="Layer_1"
                                         xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                         viewBox="0 0 512 512" xml:space="preserve">
                            <g>
                                <g>
                                    <g>
                                        <path d="M256,178.087c-12.294,0-22.261,9.966-22.261,22.261v89.043c0,12.294,9.966,22.261,22.261,22.261
                                            c12.294,0,22.261-9.967,22.261-22.261v-89.043C278.261,188.053,268.294,178.087,256,178.087z"/>
                                        <path d="M434.087,142.786l6.52,6.52c8.693,8.693,22.788,8.693,31.482,0c8.693-8.693,8.693-22.788,0-31.482l-44.522-44.522
                                            c-8.693-8.693-22.788-8.693-31.482,0s-8.693,22.788,0,31.482l6.52,6.52l-5.725,5.725c-32.98-26.988-73.845-44.698-118.619-49.142
                                            V44.522h22.261c12.294,0,22.261-9.966,22.261-22.261C322.783,9.967,312.816,0,300.522,0h-89.043
                                            c-12.294,0-22.261,9.967-22.261,22.261c0,12.294,9.966,22.261,22.261,22.261h22.261v23.366
                                            c-44.773,4.445-85.638,22.154-118.618,49.141l-5.725-5.725l6.519-6.519c8.693-8.693,8.693-22.788,0-31.482
                                            s-22.788-8.693-31.482,0l-44.522,44.522c-8.693,8.693-8.693,22.788,0,31.482c8.693,8.693,22.788,8.693,31.482,0l6.521-6.521
                                            l5.725,5.725c-31.402,38.374-50.248,87.423-50.248,140.881C33.391,412.344,133.047,512,256,512s222.609-99.656,222.609-222.609
                                            c0-53.458-18.846-102.506-50.247-140.88L434.087,142.786z M278.261,466.096v-20.879c0-12.294-9.967-22.261-22.261-22.261
                                            c-12.294,0-22.261,9.967-22.261,22.261v20.879c-80.562-10.044-144.4-73.882-154.444-154.444h20.879
                                            c12.294,0,22.261-9.967,22.261-22.261s-9.966-22.261-22.261-22.261H79.295c5.014-40.216,23.43-76.264,50.643-103.535
                                            c0.046-0.045,0.096-0.082,0.141-0.127c0.045-0.045,0.082-0.095,0.127-0.141c27.27-27.212,63.318-45.628,103.533-50.641v20.879
                                            c0,12.294,9.966,22.261,22.261,22.261c12.294,0,22.261-9.967,22.261-22.261v-20.879c40.224,5.015,76.279,23.437,103.552,50.659
                                            c0.039,0.039,0.071,0.083,0.11,0.122s0.083,0.072,0.122,0.11c27.222,27.272,45.645,63.328,50.659,103.552h-20.879
                                            c-12.294,0-22.261,9.967-22.261,22.261s9.966,22.261,22.261,22.261h20.879C422.661,392.214,358.823,456.052,278.261,466.096z"/>
                                    </g>
                                </g>
                            </g>
                            </svg>
                                    <span id="time">0:00</span>
                                </div>
                                <div onclick="getSkippedModel()" id="end-quiz" class="ml-3 d-flex align-items-center justify-content-center btn-danger rounded-sm p-1 mb-2 " style="cursor: pointer; color: #C10505; font-weight: 700; background-color: #F08D8D !important; padding: 0px 10px !important; height: 39px; margin-top: 3px">
                                    End Quiz
                                </div>
                            </div>
                        </div>

                        <div class="images" id="images"></div>
                        <div class="d-flex align-items-center justify-content-between gap-4 mx-4 flex-column"
                             id="nex-previous-btn">
                            <div onclick="questionNext()" id="question-next"
                                 class="custom-btn-size d-flex btn-success rounded-sm justify-content-center w-25 p-2 mb-2 px-5"
                                 style="cursor: pointer; background: #fff !important; font-weight: 600; width: fit-content; color: grey !important; border: 2px solid gray">
                                Next
                            </div>
                            <div onclick="previousQuestion()" id="question-prev"
                                 class="custom-btn-size d-flex align-items-center w-25 justify-content-center btn-danger rounded-sm p-2 mb-2 px-5"
                                 style="cursor: pointer; color: #000; font-weight: 600; background-color: darkgrey !important;">
                                Previous
                            </div>
                        </div>


                        <div id="totalTime" class="mb-4" style="margin-top: 20px; font-size: 1.2em; display: none">
                            <h3>Let’s see how you went!</h3>
                            <div class="d-flex justify-content-center">
                                <div class="timer" id="timer">
                                    <svg fill="#000000" height="30px" width="30px" version="1.1" id="Layer_1"
                                         xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                         viewBox="0 0 512 512" xml:space="preserve">
                            <g>
                                <g>
                                    <g>
                                        <path d="M256,178.087c-12.294,0-22.261,9.966-22.261,22.261v89.043c0,12.294,9.966,22.261,22.261,22.261
                                            c12.294,0,22.261-9.967,22.261-22.261v-89.043C278.261,188.053,268.294,178.087,256,178.087z"/>
                                        <path d="M434.087,142.786l6.52,6.52c8.693,8.693,22.788,8.693,31.482,0c8.693-8.693,8.693-22.788,0-31.482l-44.522-44.522
                                            c-8.693-8.693-22.788-8.693-31.482,0s-8.693,22.788,0,31.482l6.52,6.52l-5.725,5.725c-32.98-26.988-73.845-44.698-118.619-49.142
                                            V44.522h22.261c12.294,0,22.261-9.966,22.261-22.261C322.783,9.967,312.816,0,300.522,0h-89.043
                                            c-12.294,0-22.261,9.967-22.261,22.261c0,12.294,9.966,22.261,22.261,22.261h22.261v23.366
                                            c-44.773,4.445-85.638,22.154-118.618,49.141l-5.725-5.725l6.519-6.519c8.693-8.693,8.693-22.788,0-31.482
                                            s-22.788-8.693-31.482,0l-44.522,44.522c-8.693,8.693-8.693,22.788,0,31.482c8.693,8.693,22.788,8.693,31.482,0l6.521-6.521
                                            l5.725,5.725c-31.402,38.374-50.248,87.423-50.248,140.881C33.391,412.344,133.047,512,256,512s222.609-99.656,222.609-222.609
                                            c0-53.458-18.846-102.506-50.247-140.88L434.087,142.786z M278.261,466.096v-20.879c0-12.294-9.967-22.261-22.261-22.261
                                            c-12.294,0-22.261,9.967-22.261,22.261v20.879c-80.562-10.044-144.4-73.882-154.444-154.444h20.879
                                            c12.294,0,22.261-9.967,22.261-22.261s-9.966-22.261-22.261-22.261H79.295c5.014-40.216,23.43-76.264,50.643-103.535
                                            c0.046-0.045,0.096-0.082,0.141-0.127c0.045-0.045,0.082-0.095,0.127-0.141c27.27-27.212,63.318-45.628,103.533-50.641v20.879
                                            c0,12.294,9.966,22.261,22.261,22.261c12.294,0,22.261-9.967,22.261-22.261v-20.879c40.224,5.015,76.279,23.437,103.552,50.659
                                            c0.039,0.039,0.071,0.083,0.11,0.122s0.083,0.072,0.122,0.11c27.222,27.272,45.645,63.328,50.659,103.552h-20.879
                                            c-12.294,0-22.261,9.967-22.261,22.261s9.966,22.261,22.261,22.261h20.879C422.661,392.214,358.823,456.052,278.261,466.096z"/>
                                    </g>
                                </g>
                            </g>
                            </svg>
                                    <span id="total-time">0:00</span>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center quiz-info-total">
                                <div class="correct-total-box align-items-center">
                                    <div class="correct-total d-flex mb-3 justify-content-center align-items-center"><h1
                                            class="font-weight-bold" id="correct-total-count">0</h1></div>
                                    <div
                                        class="custom-btn-size d-flex btn-success rounded-sm justify-content-center w-25 p-1  mb-2 px-5 info-button"
                                        style="margin-right: 0; background:#C8E7A7 !important;  width: fit-content; color: #28a745 !important;">
                                        Correct
                                    </div>
                                </div>
                                <div class="custom-btn-size wrong-total-box align-items-center">
                                    <div class="wrong-total d-flex mb-3 justify-content-center align-items-center"><h1
                                            class="font-weight-bold" id="wrong-total-count">0</h1></div>
                                    <div
                                        class="d-flex align-items-center w-25 justify-content-center btn-danger rounded-sm p-1 mb-2 px-5 info-button"
                                        style="margin-left: 0; background-color: #F08D8D !important; width: fit-content; color: #C10505;">
                                        Wrong
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-center mt-3">
                                <a class="btn btn-sm dashboard-btn"
                                   href="{{ route('student.dashboard') }}">Continue to
                                    Dashboard
                                </a>
                            </div>

                            <p class="d-flex justify-content-center mt-3"><span class="text-bold px-1">Tip: </span>
                                Review
                                the questions marked incorrect in the “Wrong Question” tab and
                                discuss them with your tutor during your lessons.</p>
                        </div>
                    @else
                        @if($validity)
                            <h3 class="m-5">There are no further questions available on this topic.</h3>
                        @else
                            <h3 class="m-5">This week's 30-minute quiz has concluded! Get ready to start a new quiz next
                                week!</h3>
                            <p>For unlimited quizzes, consider purchasing a paid plan!</p>

                        @endif
                    @endif
                </div>

                <input type="hidden" id="question-skipp" value="{{ json_encode(array_map(fn($item, $index) => $index, $randomCombination, array_keys($randomCombination))) }}">
                <div class="question-progress-view" id="question-progress-view">
                    <ul class="nav nav-pills nav-sidebar flex-column progress-box" data-widget="treeview"
                        role="menu" data-accordion="false">
                    @foreach ($randomCombination as $index=>$item)
                            <li onclick="loadSkippedQuestion([{{$index}}])" class="nav-item">
                                <span class="progress-circle id=" item-{{$item['id']}}">
                                <p>{{ $loop->index + 1 }}</p>
                                </span>
                            </li>
                            <li>
                                <span class="progress-line"></span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="imgbox-bottom-btns mb-5 mt-2" id="imgbox-bottom-btns">
                    <div class="question-answer">
                        @if(!empty($randomCombination))
                            <div id="question-image" class="mb-4"></div>
                        @endif
                        @if(Auth::user()->subscription_status)
                            <div id="accordion">
                                <div class="card card-success solution-question">
                                    <div class="card-header bg-success">
                                        <h4 class="card-title w-100">
                                            <a class="d-block w-100 text-white collapsed px-4"
                                               style="cursor: pointer;font-weight: 700;width: fit-content !important;padding: 0px 17px !important; margin: 0 auto"
                                               data-toggle="collapse" href="#collapseThreeSolution"
                                               aria-expanded="false">
                                                See Solution
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="collapseThreeSolution" class="collapse" data-parent="#accordion" style="">
                                        <div class="card-body images" id="solution_images">
                                            No Solution Available for this Question
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if(!empty($question['answer_image']))
                            <div id="accordions">
                                <div class="card card-success mb-0">
                                    <div class="card-header bg-success">
                                        <h4 class="card-title w-100">
                                            <a class="d-block w-100 text-white collapsed" style="font-weight: 700" data-toggle="collapse"
                                               href="#collapseThrees" aria-expanded="false">
                                                See Working Out
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="collapseThrees" class="collapse" data-parent="#accordions" style="">
                                        <div class="card-body images" id="answer_images">
                                            No Answer Available for this Question
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    @if(!empty($randomCombination))
                        <div class="buttons" id="buttons"></div>
                    @endif
                </div>
            </div>

            <!-- Skip Modal -->
            <div id="skipModal">
                <div class="modal-content" style="margin:100px auto; width: 30%">
                    <p>You have skipped some questions without answering. Do you want to attempt them now? Press 'Attempt Skipped Questions' to complete them or 'End Quiz' to finish.</p>
                    <div class="modal-buttons">
                        <button class=" btn btn-primary rounded-sm " id="skip-yes">Attempt Skipped Questions</button>
                        <button class=" btn btn-danger rounded-sm " id="skip-no">End Quiz</button>
                    </div>
                </div>
            </div>
            <input type="hidden" id="current_question_index" value="0">
            <input type="hidden" id="counterValue" name="counterValue" value="0">
        </section>
    </div>

@endsection
@section('page-script')
    <script>
        $(document).ready(function () {
            let counter = 0;
            const counterField = document.getElementById('counterValue');
            setInterval(() => {
                counter++;
                counterField.value = counter;
            }, 1000);

            $('.popthumb').on('click', function () {
                loadPopup();
            });
        });
        var user_id = 0;
        var totalTime = 0;
        const questionsrow = [
                @foreach($randomCombination as $question)
            {
                difficulty: '{{ $question["difficulty"] }}',
                code: '{{ $question["code"] }}',
                id: {{ $question['id'] }},
                time: {{ $question['time'] / 60 }},
                images: {!! json_encode($question['quiz_image']) !!},
                solutionImages: {!! !empty($question['solution_image']) ? json_encode($question['solution_image']) : json_encode([]) !!},
                answerImages: {!! !empty($question['answer_image']) ? json_encode($question['answer_image']) : json_encode([]) !!}
            },
            @endforeach
        ];

        function shuffleArray(array) {
            for (let i = array.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [array[i], array[j]] = [array[j], array[i]]; // Swap elements
            }
            return array;
        }

        const questions = shuffleArray(questionsrow);


        let w = 100 / questions.length;
        $('#progress-bar').css('width', w + '%');

        let currentQuestionIndex = 0;
        updateProgressBar(); // Initial progress bar setup

        function updateProgressBar() {
            let progressWidth = (currentQuestionIndex + 1) / questions.length * 100; // Calculate progress
            $('#progress-bar').css('width', progressWidth + '%');
        }

        let timer;
        let remainingTime;

        function startTimer(duration) {
            remainingTime = Math.round(duration * 60);
            updateTimerDisplay();
            clearInterval(timer);
            timer = setInterval(() => {
                remainingTime--;
                updateTimerDisplay();
            }, 1000);
            $('.popthumb').on('click', function () {
                loadPopup();
            });
        }


        function updateTimerDisplay() {
            const roundedTime = Math.round(remainingTime);
            const minutes = Math.floor(Math.abs(roundedTime) / 60);
            const seconds = Math.abs(roundedTime % 60);
            const timeDisplay = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            document.getElementById('time').innerText = timeDisplay;

            // Change the color to red when time reaches 0 or below
            if (remainingTime < 0) {
                document.getElementById('time').classList.add('extra-time');
            } else {
                document.getElementById('time').classList.remove('extra-time');
            }
        }

    function loadQuestion() {
        const questionData = questions[currentQuestionIndex];
        $('#question-code').html(questionData.code);
        $('#current_question_index').val(currentQuestionIndex);

        let difficulty = questionData.difficulty;
        let modifiedDifficulty = difficulty.charAt(0).toUpperCase() + difficulty.slice(1);
        $('#question-difficulty').html(modifiedDifficulty);

        var imagesHtml = '<div class="row col-md-12 mb-4 justify-content-around">';
        var baseUrl = '{{url('/')}}';

        // Reverse the order of images
        var reversedImages = [...questionData.images];
        $.each(reversedImages, function (imgIndex, image) {
            imagesHtml += '<div class="col-md-12 mt-5"><img src="' + baseUrl + '/storage/images/' + image.image_name + '" alt="Image ' + imgIndex + '" width="auto" height="300" style="width:100%" class="popthumb"></div>';
        });
        var questionIndex = document.getElementById('current_question_index').value;
        imagesHtml += '</div>';
        document.getElementById('images').innerHTML = imagesHtml;

        document.getElementById('buttons').innerHTML = `
        <div class="d-flex align-items-center justify-content-between gap-4 mx-4 flex-column">
            <div onclick="handleAnswer('correct', '${questionIndex}')" class="custom-btn-size d-flex btn-success rounded-sm justify-content-center p-2 mb-2 px-5" style="cursor: pointer;margin-right: 0; background:#C8E7A7 !important; font-weight: 600; color: #28a745 !important;">
                Correct
            </div>
            <div onclick="handleAnswer('wrong','${questionIndex}')" class="custom-btn-size d-flex align-items-center justify-content-center btn-danger rounded-sm p-2 mb-2 px-5" style="cursor: pointer; margin-left: 0; color: #C10505; font-weight: 600; background-color: #F08D8D !important;">
                Wrong
            </div>
        </div>
    `;
        document.getElementById('question-image').innerHTML = `
        <div onclick="handleAnswer('report','${questionIndex}')" class="d-flex align-items-center justify-content-center btn-danger rounded-sm p-2 mb-2 px-5" style="cursor: pointer; color: #C10505; font-weight: 700; background-color: #F08D8D !important;">
            <i class="fa-solid fa fa-flag" style="color: #f70808; margin-right: 10px"></i>Report
        </div>`;

        // Reverse the solution images
        if (questionData.solutionImages.length > 0) {
            var solutionImagesHtml = '<div class="row">';
            var reversedSolutionImages = [...questionData.solutionImages];
            $.each(reversedSolutionImages, function (imgIndex, image) {
                solutionImagesHtml += '<div class="col-md-6 mt-3"><img src="' + baseUrl + '/storage/images/' + image.image_name + '" alt="Image ' + imgIndex + '" width="200" height="150" class="popthumb"></div>';
            });
            solutionImagesHtml += '</div>';
            document.getElementById('solution_images').innerHTML = solutionImagesHtml;
        }

        // Reverse the answer images
        if (questionData.answerImages.length > 0) {
            var answerImagesHtml = '<div class="row mb-4">';
            var reversedAnswerImages = [...questionData.answerImages];
            $.each(reversedAnswerImages, function (imgIndex, image) {
                answerImagesHtml += '<div class="col-md-12 mt-4"><img src="' + baseUrl + '/storage/images/' + image.image_name + '" alt="Image ' + imgIndex + '" width="200" height="150" class="popthumb"></div>';
            });
            answerImagesHtml += '</div>';
            document.getElementById('answer_images').innerHTML = answerImagesHtml;
        }

        startTimer(questionData.time); // Start the timer for the current question
    }


        function sendReport() {
            const questionData = questions[currentQuestionIndex];
            let qid = questionData.id
            let report_text = $('#report_text').val()
            $.ajax({
                url: '{{env('AJAX_URL')}}' + '/question-report',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    'question_id': qid,
                    'report_text': report_text
                },
                success: function (result) {
                    if (result?.success == true) {
                        toastr.success(`Question has been reported.`);
                    } else {
                        toastr.error(`Not Reported! Something went Wrong.`);
                    }

                },
                error: function (error) {
                    toastr.error(`Not Reported! Something went Wrong.`);
                }
            });
        }

        var correct = 0;
        var wrong = 0;
        var questionStatus = {};

        function updateTotalCounts() {
            let totalCorrect = 0;
            let totalWrong = 0;

            for (const questionId in questionStatus) {
                totalCorrect += questionStatus[questionId].correct;
                totalWrong += questionStatus[questionId].wrong;
            }

            document.getElementById('correct-total-count').innerText = totalCorrect;
            document.getElementById('wrong-total-count').innerText = totalWrong;
        }

        function getpreviousAns(questions, quiz_id) {
            $.ajax({
                url: '{{ env('AJAX_URL') }}' + '/question/previous/ans',
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    'question': questions,
                    'quiz_id': quiz_id,
                },
                success: function (result) {
                    if (result.html) {
                        $('.question-progress-view').html(result.html);
                    } else {
                        console.log('No HTML returned.');
                    }
                },
                error: function (error) {
                    toastr.error('Not Reported! Something went Wrong.');
                }
            });
        }

        function handleAnswer(response,questionIndex) {
            if (document.getElementById('question-skipp')) {
                let removeQuestionIndex = document.getElementById('question-skipp').value;
                if (!removeQuestionIndex || removeQuestionIndex === 'null' || removeQuestionIndex === '') {
                    console.warn('question-skipp is not properly initialized');
                } else {
                    try {
                        let NewRemoveQuestionIndex = JSON.parse(removeQuestionIndex);
                        NewRemoveQuestionIndex = NewRemoveQuestionIndex.filter(index => Number(index) !== Number(questionIndex));
                        document.getElementById('question-skipp').value = JSON.stringify(NewRemoveQuestionIndex);
                    } catch (error) {
                        console.error('Error parsing question-skipp value:', error);
                    }
                }
            } else {
                console.error('Element with id "question-skipp" not found in the DOM.');
            }
            const collapseThrees = document.getElementById('collapseThrees');
            const collapseThreeSolution = document.getElementById('collapseThreeSolution');

            if (collapseThrees) collapseThrees.classList.remove('show');
            if (collapseThreeSolution) collapseThreeSolution.classList.remove('show');

            const questionData = questions[questionIndex];
            const questionId = questionData.id;

            // Ensure questionStatus exists for this question
            if (!questionStatus[questionId]) {
                questionStatus[questionId] = {correct: 0, wrong: 0, status: ''};
            }

            const currentStatus = questionStatus[questionId].status;

            // Update status based on the response
            if (response === 'correct' && currentStatus !== 'correct') {
                if (currentStatus === 'wrong') {
                    questionStatus[questionId].wrong -= 1;
                }
                questionStatus[questionId].correct += 1;
                questionStatus[questionId].status = 'correct';
            } else if (response === 'wrong' && currentStatus !== 'wrong') {
                if (currentStatus === 'correct') {
                    questionStatus[questionId].correct -= 1;
                }
                questionStatus[questionId].wrong += 1;
                questionStatus[questionId].status = 'wrong';
            }

            // Handle report case
            if (response === 'report') {
                $("#report_text").val('');
                $('#reportModal').modal('show');
                questionStatus[questionId].status = 'report';
                return; // Skip sending data for a report
            }

            // Save the response
            const timeTaken = (questionData.time * 60) - remainingTime;
            totalTime += timeTaken;

            const payload = {
                user_id: user_id,
                question_id: questionId,
                time_taken: timeTaken,
                quiz_id: '{{$quiz_id}}',
                response: response,
            };

            fetch('{{env('AJAX_URL')}}' + '/student/save-quiz', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    _token: $('meta[name="csrf-token"]').attr('content'),
                },
                body: JSON.stringify(payload),
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success === true) {
                        toastr.success('Your answer has been saved.');
                        nextQuestion();
                        updateActiveStep();
                        updateProgressBar();
                    } else {
                        toastr.error('Something went wrong! Your answer was not saved.');
                    }
                }).then((data) => {
                    getpreviousAns(questions,'{{$quiz_id}}');
            })
                .catch((error) => {
                    console.error('Error:', error);
                    nextQuestion();
                });


            updateTotalCounts();
            loadPopup();
        }


        function updateActiveStep() {
            const steps = document.querySelectorAll('.steps li');
            if (currentQuestionIndex < steps.length) {
                steps[currentQuestionIndex - 1].classList.add('active');
            }
        }

        function getSkippedModel() {
            const skippInput = document.getElementById('question-skipp');
            let skippIndexes = JSON.parse(skippInput.value);

            // Check if skippIndexes is not null and not empty
            if (skippIndexes && Array.isArray(skippIndexes) && skippIndexes.length > 0) {
                // Show modal to ask if the user wants to skip the current question
                document.getElementById('skipModal').style.display = 'block';
                // Handle the "Yes" button click
                document.getElementById('skip-yes').addEventListener('click', function() {
                    let minQuestionIndex = Math.min(...skippIndexes);
                    $('#current_question_index').val(minQuestionIndex);
                    loadSkippedQuestion([minQuestionIndex]);
                    document.getElementById('skipModal').style.display = 'none'; // Close the modal
                });

                // Handle the "No" button click
                document.getElementById('skip-no').addEventListener('click', function() {
                    document.getElementById('skipModal').style.display = 'none'; // Close the modal
                    if(confirm("You have skipped some questions without answering. Do you want to attempt them now? Press 'Attempt Skipped Questions' to complete them or 'End Quiz' to finish.")){
                        showTotalTime(); // Show the total time if they decide not to skip
                    }

                });

            } else {
                showTotalTime();
            }
        }

        function nextQuestion() {
            currentQuestionIndex++;
            if (currentQuestionIndex < questions.length) {
                loadQuestion();
                document.getElementById('try-solution').innerText = currentQuestionIndex + 1;

                // Get the current skipp value from the input field
                const skippValue = JSON.parse($('#question-skipp').val());
                $('#question-skipp').val(JSON.stringify(skippValue));

            } else {
                console.log('No question nex');
            }
        }

        function questionNext() {
            const collapseThrees = document.getElementById('collapseThrees');
            const collapseThreeSolution = document.getElementById('collapseThreeSolution');

            if (collapseThrees) collapseThrees.classList.remove('show');
            if (collapseThreeSolution) collapseThreeSolution.classList.remove('show');

            currentQuestionIndex = document.getElementById('current_question_index').value;
            if (currentQuestionIndex <= questions.length-2) {
                // Calculate time taken
                const questionData = questions[currentQuestionIndex];
                const timeTaken = (questionData.time * 60) - remainingTime;
                totalTime += timeTaken;

                currentQuestionIndex++; // Move to the next question

                loadQuestion();
                updateProgressBar();
                document.getElementById('try-solution').innerText = currentQuestionIndex + 1;
            } else {
                console.log('No question nex');
            }

            // Update button states
            updateButtonStates();
        }

        function previousQuestion() {
            const collapseThrees = document.getElementById('collapseThrees');
            const collapseThreeSolution = document.getElementById('collapseThreeSolution');

            if (collapseThrees) collapseThrees.classList.remove('show');
            if (collapseThreeSolution) collapseThreeSolution.classList.remove('show');

            if (currentQuestionIndex > 0) {
                currentQuestionIndex = document.getElementById('current_question_index').value;
                // Calculate time taken
                const questionData = questions[currentQuestionIndex];
                const timeTaken = (questionData.time * 60) - remainingTime;
                totalTime += timeTaken;

                currentQuestionIndex--;
                loadQuestion();
                updateProgressBar();

                document.getElementById('try-solution').innerText = currentQuestionIndex + 1;

            } else {
                console.log('Already at the first question.');
            }

            // Update button states
            updateButtonStates();
        }

        function updateButtonStates() {
            const nextButton = document.getElementById('next-button');
            const previousButton = document.getElementById('previous-button');

            if (nextButton) {
                nextButton.disabled = currentQuestionIndex >= questions.length - 1;
            }
            if (previousButton) {
                previousButton.disabled = currentQuestionIndex <= 0;
            }
        }

        function showTotalTime() {
            clearInterval(timer);
            var counterValue = $('#counterValue').val();
            const roundedTotalTime = Math.round(counterValue);
            const totalMinutes = Math.floor(roundedTotalTime / 60);
            const totalSeconds = roundedTotalTime % 60;
            const lastLi = document.querySelector('#li-steps li:last-child');
            if (lastLi) {
                lastLi.classList.add('active');
            }
            document.getElementById('totalTime').style.display = 'block';
            document.getElementById('question-progress-view').style.display = 'none'
            document.getElementById('images').innerHTML = '';

            document.getElementById('question-code-box').innerHTML = '';
            document.getElementById('imgbox-bottom-btns').innerHTML = '';

            document.getElementById('custom-progress-bar').innerHTML = '';
            document.getElementById('custom-progress-bar').style.display = 'none';
            document.getElementById('total-time').innerHTML = `${totalMinutes} : ${totalSeconds.toString().padStart(2, '0')}`;

            document.getElementById('nex-previous-btn').style.setProperty('display', 'none', 'important');


            if ($('#accordions').length) {
                $('#accordions').css('display', 'none');
            }
            if ($('#accordion').length) {
                $('#accordion').css('display', 'none');
            }
            document.getElementById('li-steps').style.display = 'none';
            document.getElementById('accordion').innerHTML = '';
            document.getElementById('accordions').innerHTML = '';
        }

        function loadSkippedQuestion(indexes) {
            const collapseThrees = document.getElementById('collapseThrees');
            const collapseThreeSolution = document.getElementById('collapseThreeSolution');
            if (collapseThrees) collapseThrees.classList.remove('show');
            if (collapseThreeSolution) collapseThreeSolution.classList.remove('show');
            let updatedText = '';
            indexes.forEach(function (index) {
                updatedText += `${index + 1} `;
                $('#current_question_index').val(index);
                const questionData = questions[index];
                $('#question-code').html(questionData.code);
                let difficulty = questionData.difficulty;
                let modifiedDifficulty = difficulty.charAt(0).toUpperCase() + difficulty.slice(1);
                $('#question-difficulty').html(modifiedDifficulty);
                let imagesHtml = '<div class="row col-md-12 mb-4 justify-content-around">';
                var baseUrl = '{{url('/')}}';
                $.each(questionData.images, function (imgIndex, image) {
                    imagesHtml += '<div class="col-md-12 mt-5"><img src="' + baseUrl + '/storage/images/' + image.image_name + '" alt="Image ' + imgIndex + '" width="auto" height="300" style="width:100%" class="popthumb"></div>';
                });
                imagesHtml += '</div>';
                document.getElementById('images').innerHTML = imagesHtml;

                document.getElementById('buttons').innerHTML = `
            <div class="d-flex align-items-center justify-content-between gap-4 mx-4 flex-column">
                <div onclick="handleAnswer('correct','${index}')" class="d-flex btn-success rounded-sm justify-content-center w-25 p-2 mb-2 px-5" style="cursor: pointer;margin-right: 0; background:#C8E7A7 !important; font-weight: 600; width: fit-content; color: #28a745 !important;">
                    Correct
                </div>
                <div onclick="handleAnswer('wrong','${index}')" class="d-flex align-items-center w-25 justify-content-center btn-danger rounded-sm p-2 mb-2 px-5" style="cursor: pointer; margin-left: 0; color: #C10505; font-weight: 600; background-color: #F08D8D !important;">
                    Wrong
                </div>
            </div>
        `;

                document.getElementById('question-image').innerHTML = `
            <div onclick="handleAnswer('report','${index}')" class="d-flex align-items-center justify-content-center btn-danger rounded-sm p-2 mb-2 px-5" style="cursor: pointer; color: #C10505; font-weight: 700; background-color: #F08D8D !important;">
                <i class="fa-solid fa fa-flag" style="color: #f70808; margin-right: 10px"></i>Report
            </div>
        `;

                if (questionData.solutionImages.length > 0) {
                    var solutionImagesHtml = '<div class="row">';
                    $.each(questionData.solutionImages, function (imgIndex, image) {
                        solutionImagesHtml += '<div class="col-md-6 mt-3"><img src="' + baseUrl + '/storage/images/' + image.image_name + '" alt="Image ' + imgIndex + '" width="200" height="150" class="popthumb"></div>';
                    });
                    solutionImagesHtml += '</div>';
                    document.getElementById('solution_images').innerHTML = solutionImagesHtml;
                }

                if (questionData.answerImages.length > 0) {
                    var answerImagesHtml = '<div class="row mb-4">';
                    $.each(questionData.answerImages, function (imgIndex, image) {
                        answerImagesHtml += '<div class="col-md-12 mt-4"><img src="' + baseUrl + '/storage/images/' + image.image_name + '" alt="Image ' + imgIndex + '" width="200" height="150" class="popthumb"></div>';
                    });
                    answerImagesHtml += '</div>';
                    document.getElementById('answer_images').innerHTML = answerImagesHtml;
                }
                startTimer(questionData.time);
            });
            document.getElementById('try-solution').innerText = updatedText.trim();
            let progressWidth = updatedText / questions.length * 100; // Calculate progress
            $('#progress-bar').css('width', progressWidth + '%');
        }


        window.onload = loadQuestion;
    </script>

    <link rel="stylesheet" href="{{url('assets/plugins/toastr/toastr.css')}}">
    <script src="{{url('assets/plugins/toastr/toastr.min.js')}}"></script>


    <div class="modal fade" id="reportModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Detail!</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <textarea placeholder="Add More Detail For Reporting" class="form-control" id="report_text"
                              name="report_text"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" onclick="sendReport();" class="btn btn-primary" data-dismiss="modal">Submit
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
