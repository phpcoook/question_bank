<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\QuestionImage;
use Yajra\DataTables\DataTables;

class QuizController extends Controller
{

    public function startQuiz($target = 30)
    {
        $attended = Quiz::where('user_id', Auth::user()->id)->where('answer', 'correct')->get();
        if ($attended->count() > 0) {
            $notIn = $attended->pluck('question_id');
            $questions = Question::with('quizImage')->select('id', 'time')->whereNotIn('id', $notIn)->get()->toArray();
        } else {
            $questions = Question::with('quizImage')->select('id', 'time')->get()->toArray();
        }
        $result = [];
        $this->findCombinations($questions, $target, 0, [], $result);
        $randomCombination = !empty($result) ? $result[array_rand($result)] : [];

        return view('student.quiz', compact('randomCombination'));
    }

    private function findCombinations(
        array $questions,
        int $target,
        int $start,
        array $currentCombination,
        array &$result
    ) {
        if ($target >= 0) {
            if ($target === 0) {
                $result[] = $currentCombination;
                return;
            }
            for ($i = $start; $i < count($questions); $i++) {
                $time = $questions[$i]['time'];
                if ($time <= $target) {
                    $this->findCombinations($questions, $target - $time, $i + 1,
                        array_merge($currentCombination, [$questions[$i]]), $result);
                }
            }
            if ($target > 0 && !empty($currentCombination)) {
                $result[] = $currentCombination;
            }
        }
    }





    public function startQuizq($target = 30)
    {
        $attended = Quiz::where('user_id', Auth::user()->id)->where('answer', 'correct')->get();
        if ($attended->count() > 0) {
            $notIn = $attended->pluck('question_id');
            $questions = Question::select('id', 'question', 'time')->whereNotIn('id', $notIn)->get()->toArray();
        } else {
            $questions = Question::select('id', 'question', 'time')->get()->toArray();
        }
        $result = [];
        $this->findCombinations($questions, $target, 0, [], $result);
        $randomCombination = !empty($result) ? $result[array_rand($result)] : [];
        return view('student.quiz', compact('randomCombination'));
    }

    private function findCombinationsq(
        array $questions,
        int $target,
        int $start,
        array $currentCombination,
        array &$result
    ) {
        if ($target === 0) {
            $result[] = $currentCombination;
            return;
        }
        for ($i = $start; $i < count($questions); $i++) {
            $time = $questions[$i]['time'];
            if ($time < $target) {
                $this->findCombinations($questions, $target - $time, $i + 1,
                    array_merge($currentCombination, [$questions[$i]]), $result);
            }
        }
    }

    public function saveQuiz(Request $request)
    {
        try {

            $quiz = Quiz::upsert(
                [
                    'answer' => $request->response,
                    'time' => $request->time_taken,
                     'user_id' => Auth::user()->id,
                    'question_id' => $request->question_id
                ],
                [
                    'user_id' => Auth::user()->id,
                    'question_id' => $request->question_id
                ]

            );
            if ($quiz) {
                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false]);
            }
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return response()->json(['success' => false]);
        }
    }


}
