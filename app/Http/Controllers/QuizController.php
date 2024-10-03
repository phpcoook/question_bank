<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Quiz;
use App\Models\Reported;
use App\Models\Setting;
use App\Models\Subscription;
use App\Models\SubTopic;
use App\Models\Topic;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\QuestionImage;
use Yajra\DataTables\DataTables;

class QuizController extends Controller
{

    public function startQuiz(Request $request)
    {
        try {

            $subscription = Subscription::where('user_id', Auth::user()->id)->whereDate('end_date', '>', now())->first();
            if (empty($subscription)) {
                $time = Setting::first();
                $currentDate = Carbon::now();
                $endOfWeek = $currentDate->endOfWeek();
                $endDate = $endOfWeek->toDateTimeString();
                $currentDates = Carbon::now();
                $startOfWeek = $currentDates->startOfWeek(Carbon::SUNDAY);
                $startDate = $startOfWeek->toDateTimeString();
                $totalMinutes = Quiz::where('user_id', Auth::user()->id)
                    ->whereBetween('quiz.created_at', [$startDate, $endDate])
                    ->join('question','question.id','quiz.question_id')
                    ->sum('question.time');
                if ($totalMinutes >= $time->no_of_questions) {
                    $validity = false;
                    $randomCombination = [];
                    $quiz_id = date('Ymdhis') . rand(0, 1000);
                    return view('student.quiz', compact('randomCombination', 'validity', 'quiz_id'));
                }
            }
            $target = $request->time ?? 30;
//            if (count($request->sub_topics) !== 0 && count($request->sub_topics) > 5) {
//                return redirect()->back()->with('error', 'Please select minimum 1 and maximum 5 topics');
//            }
            $attended = Quiz::where('user_id', Auth::user()->id)->where('answer', 'correct')->get();
            if ($attended->count() > 0) {
                $notIn = $attended->pluck('question_id');
                $questions = Question::with('quizImage')
                    ->select('id', 'time')
                    ->where('reported', '0')
                    ->where('difficulty', $request->difficulty)
                    ->where('std', Auth::user()->std)
                    ->whereNotIn('id', $notIn)
                    ->where(function ($query) use ($request) {
                        foreach ($request->sub_topics as $sub_topic) {
                            $query->orWhereRaw('JSON_CONTAINS(subtopic_id, ?)', [json_encode($sub_topic)]);
                        }
                    });

            } else {
                $questions = Question::with('quizImage')
                    ->select('id', 'time')
                    ->where('reported', '0')
                    ->where('difficulty', $request->difficulty)
                    ->where('std', Auth::user()->std)
                    ->where(function ($query) use ($request) {
                        foreach ($request->sub_topics as $sub_topic) {
                            $query->orWhereRaw('JSON_CONTAINS(subtopic_id, ?)', [json_encode($sub_topic)]);
                        }
                    });
            }
            $result = [];

            $subscription = Subscription::where('user_id',Auth::user()->id)->whereDate('end_date', '>', now())->count();
            if($subscription){
                $questions->with('ansImage');
            }

            $this->findCombinations($questions->get()->toArray(), $target, 0, [], $result);

            $randomCombination = !empty($result) ? $result[array_rand($result)] : [];
            $validity = true;
            $quiz_id = date('Ymdhis') . rand(0, 1000);

            return view('student.quiz', compact('randomCombination', 'validity', 'quiz_id'));
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            $randomCombination = [];
            $validity = true;
            $quiz_id = date('Ymdhis') . rand(0, 1000);
            return view('student.quiz', compact('randomCombination', 'validity', 'quiz_id'));
        }
    }

    private function findCombinations(
        array $questions,
        int $target,
        int $start,
        array $currentCombination,
        array &$result
    ) {
        try {
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
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return [];
        }
    }


    public function startQuizq($target = 30)
    {
        $attended = Quiz::where('user_id', Auth::user()->id)->where('answer', 'correct')->get();
        if ($attended->count() > 0) {
            $notIn = $attended->pluck('question_id');
            $questions = Question::select('id', 'question', 'time')->whereNotIn('id', $notIn)->orderBy('time',
                'ASC')->get()->toArray();
        } else {
            $questions = Question::select('id', 'question', 'time')->orderBy('time', 'ASC')->get()->toArray();
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
                    'question_id' => $request->question_id,
                    'quiz_id' => $request->quiz_id
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

    public function addTime()
    {
        $time = Setting::first();
        $topics = Topic::all();
        return view('student.addtime', compact("time", "topics"));
    }

    public function reportQuestion(Request $request)
    {
        try {

            $report = Reported::upsert(
                [
                    'report_text' => $request->report_text,
                    'user_id' => Auth::user()->id,
                    'question_id' => $request->question_id
                ],
                [
                    'user_id' => Auth::user()->id,
                    'question_id' => $request->question_id
                ]

            );
            if ($report) {
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
