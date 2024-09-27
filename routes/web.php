<?php

use App\Http\Controllers\QuizController;
use App\Http\Controllers\SubTopicController;
use App\Http\Controllers\TopicController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuestionBanController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TutorController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('login');
});
Route::get('/admin/login', [UserController::class, 'loginView'])->name('admin.login');
Route::post('/admin/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

//login
Route::get('/login', [StudentController::class, 'loginView'])->name('login');
Route::post('/login', [StudentController::class, 'login']);
Route::post('getSubTopicDatas', [SubTopicController::class, 'getDataByIds']);

Route::middleware(['auth'])->group(function () {
    Route::middleware(['admin'])->group(function () {
    // topic
        Route::get('/create/topic', [TopicController::class, 'create'])->name('create.topic');
        Route::Post('/topic/story', [TopicController::class, 'store'])->name('topic.story');
        Route::get('/topics', [TopicController::class, 'index'])->name('topic.index');
        Route::get('/topic/{id}/edit', [TopicController::class, 'edit'])->name('topic.edit');
        Route::put('/topic/{id}', [TopicController::class, 'update'])->name('topic.update');
        Route::delete('/topic/{id}', [TopicController::class, 'destroy'])->name('topic.destroy');
        Route::get('topics/data', [TopicController::class, 'getData'])->name('topics.data');


        // sub topic
        Route::get('/create/sub-topic', [SubTopicController::class, 'create'])->name('create.sub-topic');
        Route::Post('/sub-topic/story', [SubTopicController::class, 'store'])->name('sub-topic.story');
        Route::get('/sub-topics', [SubTopicController::class, 'index'])->name('sub-topic.index');
        Route::get('/sub-topic/{id}/edit', [SubTopicController::class, 'edit'])->name('sub-topic.edit');
        Route::put('/sub-topic/{id}', [SubTopicController::class, 'update'])->name('sub-topic.update');
        Route::delete('/sub-topic/{id}', [SubTopicController::class, 'destroy'])->name('sub-topic.destroy');
        Route::get('sub-topics/data', [SubTopicController::class, 'getData'])->name('sub-topics.data');
        Route::post('getSelectedSubTopicData', [SubTopicController::class, 'getSelectedDataByIds']);

        // question
        Route::get('/create/question', [QuestionBanController::class, 'create'])->name('create.question');
        Route::Post('/question/story', [QuestionBanController::class, 'store'])->name('question.story');
        Route::get('/questions', [QuestionBanController::class, 'index'])->name('question.index');
        Route::get('/question/{id}/edit', [QuestionBanController::class, 'edit'])->name('question.edit');
        Route::put('/question/{id}', [QuestionBanController::class, 'update'])->name('question.update');
        Route::delete('/question/{id}', [QuestionBanController::class, 'destroy'])->name('question.destroy');
        Route::get('questions/data', [QuestionBanController::class, 'getQuestionsData'])->name('questions.data');


// student
        Route::get('/create/student', [StudentController::class, 'create'])->name('create.student');
        Route::post('/create/student', [StudentController::class, 'store'])->name('store.student');
        Route::get('/students', [StudentController::class, 'index'])->name('student.index');
        Route::get('/student/{id}/edit', [StudentController::class, 'edit'])->name('student.edit');
        Route::put('/student/{id}', [StudentController::class, 'update'])->name('student.update');
        Route::delete('/student/{id}', [StudentController::class, 'destroy'])->name('student.destroy');
        Route::get('/student/data', [StudentController::class, 'getStudentData'])->name('student.data');

// Tutor
        Route::get('/create/tutor', [TutorController::class, 'create'])->name('create.tutor');
        Route::post('/create/tutor', [TutorController::class, 'store'])->name('store.tutor');
        Route::get('/tutors', [TutorController::class, 'index'])->name('tutor.index');
        Route::get('/tutor/{id}/edit', [TutorController::class, 'edit'])->name('tutor.edit');
        Route::put('/tutor/{id}', [TutorController::class, 'update'])->name('tutor.update');
        Route::delete('/tutor/{id}', [TutorController::class, 'destroy'])->name('tutor.destroy');
        Route::get('/tutor/data', [TutorController::class, 'getTutorData'])->name('tutor.data');

//setting
        Route::get('/create/setting', [SettingController::class, 'create'])->name('create.setting');
        Route::put('/setting/{id}', [SettingController::class, 'update'])->name('setting.update');
    });

//subtopic
    Route::post('getSubTopicData', [SubTopicController::class, 'getDataByIds']);
    Route::post('/getTopics', [SubTopicController::class, 'getTopics'])->name('getTopics');


// Tutor
    Route::post('/question/data', [TutorController::class, 'getQuestionData'])->name('question.data');

    Route::middleware(['student'])->group(function () {
        Route::get('/student/dashboard', [StudentController::class, 'dashboard'])->name('student.dashboard');
        Route::post('/student/start-quiz', [QuizController::class, 'startQuiz'])->name('student.start-quiz');
        Route::post('/student/save-quiz', [QuizController::class, 'saveQuiz'])->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
        Route::get('/student/wrong/question', [StudentController::class, 'wrongQuestion'])->name('student.wrong-question');
    });
    Route::get('/student/start-quiz/time', [QuizController::class, 'addTime'])->name('student.start-quiz.addtime');
    Route::middleware(['tutor'])->group(function () {
        Route::get('/tutor/dashboard', [TutorController::class, 'dashboard'])->name('tutor.dashboard');
    });
});
