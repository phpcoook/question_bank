<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuestionBanController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TutorController;
use App\Http\Controllers\UserController;

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

Route::middleware(['auth'])->group(function () {
    Route::middleware(['admin'])->group(function () {
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

    });

    Route::get('/student/dashboard', [StudentController::class, 'dashboard'])->name('student.dashboard');
    Route::get('/tutor/dashboard', [TutorController::class, 'dashboard'])->name('tutor.dashboard');
});
