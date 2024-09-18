@extends('layouts.layoutMaster')
@section('title',env('WEB_NAME').' | Student Quiz')
@section('content')
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
                <div class="question" id="question"></div>
                <div class="buttons" id="buttons"></div>
                <div id="totalTime" class="mb-4" style="margin-top: 20px; font-size: 1.2em;"></div>
                @else
                    <h3 class="m-5">Awesome! You answered all the questions!</h3>
                @endif
            </div>
        </section>
    </div>
@endsection

@section('page-script')
    <script>

        var user_id = 0;
        var totalTime = 0
        const questions = [
                @foreach($randomCombination as $question)
            { id: {{ $question['id'] }}, question: "{{ $question['question'] }}", time: {{ $question['time'] }} },
            @endforeach
        ];

        let currentQuestionIndex = 0;
        let timer;
        let elapsedTime = 0;

        function startTimer(duration) {
            timer = setInterval(() => {
                elapsedTime++;
                updateTimerDisplay();
                if (elapsedTime >= duration * 60) {
                    document.getElementById('time').classList.add('extra-time');
                }
            }, 1000);
        }

        function updateTimerDisplay() {
            const minutes = Math.floor(elapsedTime / 60);
            const seconds = elapsedTime % 60;
            document.getElementById('time').innerText = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
        }

        function loadQuestion() {
            const questionData = questions[currentQuestionIndex];
            document.getElementById('question').innerText = questionData.question;
            document.getElementById('buttons').innerHTML = `
        <button class="btn btn-success" onclick="handleAnswer('correct')">Correct</button>
        <button class="btn btn-danger mx-2" onclick="handleAnswer('wrong')">Wrong</button>
    `;
            elapsedTime = 0;
            updateTimerDisplay();
            startTimer(questionData.time);
        }

        function handleAnswer(response) {
            clearInterval(timer);
            alert(`You answered ${response}. Time allocated: ${questions[currentQuestionIndex].time} minutes.`);
            nextQuestion();
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
            const totalMinutes = Math.floor(totalTime / 60);
            const totalSeconds = totalTime % 60;
            document.getElementById('totalTime').innerHTML = `Thank you for participating in the quiz! Total time taken: ${totalMinutes} minutes and ${totalSeconds} seconds.`;
            document.getElementById('question').innerText = '';
            document.getElementById('buttons').innerHTML = '';
            document.getElementById('time').innerText = '0:00';
        }


        window.onload = loadQuestion;

        function handleAnswer(response) {
            clearInterval(timer);
            const questionData = questions[currentQuestionIndex];
            const timeTaken = elapsedTime;
            totalTime += elapsedTime
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
                body: JSON.stringify({ response, ...payload }),
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success == true) {
                        toastr.success(`You answered saved.`);
                        nextQuestion();
                    }else{
                        toastr.error(`Something Went Wrong! Answer is not save`);
                    }

                })
                .catch((error) => {
                    console.error('Error:', error);
                    toastr.error(`An error occurred while submitting your answer. Please try again.`);
                    nextQuestion();
                });
        }

    </script>
    <link rel="stylesheet" href="{{url('assets/plugins/toastr/toastr.css')}}">
    <script src="{{url('assets/plugins/toastr/toastr.min.js')}}"></script>

@endsection
