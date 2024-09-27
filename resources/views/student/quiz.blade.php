@extends('layouts.layoutMaster')
@section('title',env('WEB_NAME').' | Student Quiz')
@section('page-style')
    <style>
        .question {
            margin-bottom: 20px;
            font-size: 1.5em;
        }

        .buttons {
            margin-top: 10px;
        }

        .timer {
            font-size: 1.2em;
            margin-bottom: 20px;
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
        .displayNone{
            display: none;
        }
    </style>

@endsection
@section('content')
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

            <div class="card card-primary text-center">
                @if(!empty($randomCombination))
                    <div class="form-wizard">
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
                    <h3 class="mt-5" id="try-answer">Try to Answer in given time!</h3>
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
                        <br> Timer: <span id="time">0:00</span></div>
                    <div class="images" id="images"></div>
                    <div class="buttons" id="buttons"></div>
                    <div id="totalTime" class="mb-4" style="margin-top: 20px; font-size: 1.2em;"></div>
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
        </section>
    </div>
@endsection

@section('page-script')
    <script>
        var user_id = 0;
        var totalTime = 0;
        const questions = [
                @foreach($randomCombination as $question)
            {
                id: {{ $question['id'] }},
                time: {{ $question['time'] }},
                images: {!! json_encode($question['quiz_image']) !!} },
            @endforeach
        ];

        let currentQuestionIndex = 0;
        let timer;
        let remainingTime;

        function startTimer(duration) {
            remainingTime = duration * 60;
            updateTimerDisplay();
            clearInterval(timer);
            timer = setInterval(() => {
                remainingTime--;
                updateTimerDisplay();
            }, 1000);
        }

        function updateTimerDisplay() {
            const minutes = Math.floor(Math.abs(remainingTime) / 60); // Use abs for negative values
            const seconds = Math.abs(remainingTime % 60); // Use abs for negative values
            const timeDisplay = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
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
            var imagesHtml = '<div class="row col-md-12">';
            var baseUrl = '{{url('/')}}' + '/';
            $.each(questionData.images, function (imgIndex, image) {
                imagesHtml += '<div class="col-md-4 mt-2"><img src="' + baseUrl + 'storage/images/' + image.image_name + '" alt="Image ' + imgIndex + '" width="200" height="150"></div>';
            });
            imagesHtml += '</div>';
            document.getElementById('images').innerHTML = imagesHtml;
            document.getElementById('buttons').innerHTML = `
<div class="d-flex justify-content-center gap-4 align-items-center">
           <div onclick="handleAnswer('correct')" class="d-flex align-items-center" style="cursor: pointer;margin-right: 15px;">
    <i class="fas fa-check" style="color: green; margin-right: 5px; font-size: 1.5em;"></i>Correct
</div>
<div onclick="handleAnswer('wrong')" class="d-flex align-items-center" style="cursor: pointer; margin-left: 10px;">
    <i class="fas fa-times" style="color: red; margin-right: 5px; font-size: 1.5em;"></i>Wrong
</div>
<div onclick="handleAnswer('report')" class="d-flex align-items-center" style="cursor: pointer; margin-left: 10px;">
    <i class="fas fa-ban" style="color: red; margin-right: 5px; font-size: 1.5em;"></i>Report Question!
</div>
</div>
        `;
            startTimer(questionData.time); // Start the timer for the current question
        }

        function sendReport() {
            const questionData = questions[currentQuestionIndex];
            let qid = questionData.id
            let report_text = $('#report_text').val()
            $.ajax({
                url: "{{ url('question-report') }}",
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

        function handleAnswer(response) {
            const questionData = questions[currentQuestionIndex];
            if (response === 'report') {
                $('#reportModal').modal('show');
            } else {

                const timeTaken = (questionData.time * 60) - remainingTime; // Calculate time taken
                totalTime += timeTaken; // Update total time

                const payload = {
                    user_id: user_id,
                    question_id: questionData.id,
                    time_taken: timeTaken
                };

                fetch('{{url('student/save-quiz')}}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    body: JSON.stringify({response, ...payload}),
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success == true) {
                            toastr.success(`Your answer has been saved.`);
                            nextQuestion(); // Move to the next question
                            updateActiveStep();
                        } else {
                            toastr.error(`Something went wrong! Your answer was not saved.`);
                        }
                    })
                    .catch((error) => {
                        console.error('Error:', error);
                        toastr.error(`An error occurred while submitting your answer. Please try again.`);
                        nextQuestion(); // Proceed to the next question even if there's an error
                    });
            }
        }

        function updateActiveStep() {
            const steps = document.querySelectorAll('.steps li');
            if (currentQuestionIndex < steps.length) {
                steps[currentQuestionIndex - 1].classList.add('active');
            }
        }

        function nextQuestion() {
            currentQuestionIndex++;
            if (currentQuestionIndex < questions.length) {
                loadQuestion();
            } else {
                showTotalTime();
            }
        }

        function showTotalTime() {
            clearInterval(timer);
            const totalMinutes = Math.floor(totalTime / 60);
            const totalSeconds = totalTime % 60;
            const lastLi = document.querySelector('#li-steps li:last-child');
            if (lastLi) {
                lastLi.classList.add('active');
            }
            document.getElementById('totalTime').innerHTML = `Thank you for participating in the quiz! Total time taken: ${totalMinutes} minutes and ${totalSeconds} seconds.`;
            document.getElementById('buttons').innerHTML = '';
            document.getElementById('time').innerText = '0:00'; // Reset time display after quiz completion
            document.getElementById('images').innerHTML = '';

            document.getElementById('try-answer').style.display = 'none';
            document.getElementById('timer').style.display = 'none';
            document.getElementById('li-steps').style.display = 'none';
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
                    <textarea placeholder="Add More Detail For Reporting" class="form-control" id="report_text" name="report_text"></textarea>
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