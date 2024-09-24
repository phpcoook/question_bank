@extends('layouts.layoutMaster')
@section('title',env('WEB_NAME').' | Student Quiz')
@section('page-style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
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
                    <h3 class="mt-5">Try to Answer in given time!</h3>
                    <div class="timer" id="timer">Time: <span id="time">0:00</span></div>
                    <div class="images" id="images"></div>
                    <div class="buttons" id="buttons"></div>
                    <div id="totalTime" class="mb-4" style="margin-top: 20px; font-size: 1.2em;"></div>
                @else
                    @if($validity)
                    <h3 class="m-5">Awesome! You answered all the questions!</h3>
                    @else
                        <h3 class="m-5">This week's 30-minute quiz has concluded! Get ready to start a new quiz next week!</h3>
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
            remainingTime = duration * 60; // Set remaining time in seconds
            updateTimerDisplay(); // Set the initial display to the starting time
            clearInterval(timer); // Clear any existing timer
            timer = setInterval(() => {
                remainingTime--; // Decrement remaining time
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
<!--            <button class="btn btn-success" onclick="handleAnswer('correct')">Correct</button>-->
<!--            <button class="btn btn-danger mx-2" onclick="handleAnswer('wrong')">Wrong</button>-->
<div class="d-flex justify-content-center gap-4 align-items-center">
           <div onclick="handleAnswer('correct')" class="d-flex align-items-center" style="cursor: pointer;margin-right: 15px;">
    <i class="fas fa-check" style="color: green; margin-right: 5px; font-size: 1.5em;"></i>Correct
</div>
<div onclick="handleAnswer('wrong')" class="d-flex align-items-center" style="cursor: pointer; margin-left: 10px;">
    <i class="fas fa-times" style="color: red; margin-right: 5px; font-size: 1.5em;"></i>Wrong
</div>
</div>
        `;
            startTimer(questionData.time); // Start the timer for the current question
        }

        function handleAnswer(response) {
            // Don't clear the timer, just collect the data
            const questionData = questions[currentQuestionIndex];
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
            document.getElementById('totalTime').innerHTML = `Thank you for participating in the quiz! Total time taken: ${totalMinutes} minutes and ${totalSeconds} seconds.`;
            document.getElementById('buttons').innerHTML = '';
            document.getElementById('time').innerText = '0:00'; // Reset time display after quiz completion
            document.getElementById('images').innerHTML = '';
        }

        window.onload = loadQuestion;
    </script>

    <link rel="stylesheet" href="{{url('assets/plugins/toastr/toastr.css')}}">
    <script src="{{url('assets/plugins/toastr/toastr.min.js')}}"></script>

@endsection
