<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizTime;
use App\Models\Reported;
use App\Models\Setting;
use App\Models\Subscription;
use App\Models\Topic;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\CustomService;

class QuizController extends Controller
{

    public function startQuiz(Request $request)
    {
        try {
            if (!CustomService::checkSubscription()) {
                $time = Setting::first();
                $currentDate = Carbon::now();
                $endOfWeek = $currentDate->endOfWeek();
                $endDate = $endOfWeek->toDateTimeString();
                $currentDates = Carbon::now();
                $startOfWeek = $currentDates->startOfWeek(Carbon::SUNDAY);
                $startDate = $startOfWeek->toDateTimeString();

                $userQuizTime = QuizTime::where('user_id', Auth()->user()->id)
                    ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                    ->first();

                if ($userQuizTime) {
                    $userQuizTime->time += $request->time;
                    $userQuizTime->save();
                    if ($userQuizTime->time >= $time->no_of_questions) {
                        $validity = false;
                        $randomCombination = [];
                        $quiz_id = date('Ymdhis') . rand(0, 1000);
                        return view('student.quiz', compact('randomCombination', 'validity', 'quiz_id'));
                    }
                } else {
                    $quizTime = new QuizTime();
                    $quizTime->user_id = Auth()->user()->id;
                    $quizTime->time = $request->time;
                    $quizTime->save();
                }
            }
            $target = $request->time ?? 30;

            $stdValues = is_string(Auth::user()->std) ? json_decode(Auth::user()->std, true) : Auth::user()->std;
            $subTopics = is_array($request->sub_topics) ? $request->sub_topics : json_decode($request->sub_topics, true);

            $attended = Quiz::where('user_id', Auth::user()->id)
                ->where('answer', 'correct')
                ->get();

            if ($attended->count() > 0) {
                $notIn = $attended->pluck('question_id');

                $questionsQuery = Question::with('quizImage')
                    ->when(CustomService::checkSubscription(), function ($query) {
                        return $query->with('solutionImage');
                    })
                    ->with('answerImage')
                    ->select('id', 'time', 'code', 'difficulty')
                    ->where('reported', '0')
                    ->where(function ($query) use ($stdValues) {
                        foreach ($stdValues as $stdValue) {
                            $query->orWhereRaw('JSON_CONTAINS(std, ?)', [json_encode($stdValue)]);
                        }
                    })
                    ->whereNotIn('id', $notIn)
                    ->where(function ($query) use ($subTopics) {
                        foreach ($subTopics as $sub_topic) {
                            $query->orWhereRaw('JSON_CONTAINS(subtopic_id, ?)', [json_encode($sub_topic)]);
                        }
                    });

                if (CustomService::checkSubscription()) {
                    $questionsQuery->with('solutionImage');
                }

                $questions = $questionsQuery->get();  // Convert the query into a collection
            } else {
                $questionsQuery = Question::with('quizImage')
                    ->when(CustomService::checkSubscription(), function ($query) {
                        return $query->with('solutionImage');
                    })
                    ->with('answerImage')
                    ->select('id', 'time', 'code', 'difficulty')
                    ->where('reported', '0')
                    ->where(function ($query) use ($stdValues) {
                        foreach ($stdValues as $stdValue) {
                            $query->orWhereRaw('JSON_CONTAINS(std, ?)', [json_encode($stdValue)]);
                        }
                    })
                    ->where(function ($query) use ($subTopics) {
                        foreach ($subTopics as $sub_topic) {
                            $query->orWhereRaw('JSON_CONTAINS(subtopic_id, ?)', [json_encode($sub_topic)]);
                        }
                    });

                if (CustomService::checkSubscription()) {
                    $questionsQuery->with('solutionImage');
                }

                $questions = $questionsQuery->get();  // Convert the query into a collection
            }

            $result = $this->findCombinations($questions->toArray(), $target * 60);
            $randomCombination = $result;
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
        array $data,
        int $target
    )
    {
        try {
            $totalTime = 0;
            $selectedQuizzes = [];
            $uniqueQuestions = [];


            foreach ($data as $quiz) {
                if ($totalTime + $quiz['time'] <= $target) {
                    $totalTime += $quiz['time'];
                    $selectedQuizzes[] = $quiz;
                    foreach ($quiz['quiz_image'] as $image) {
                        $uniqueQuestions[$image['question_id']] = $image;
                    }
                }
            }

            if ($totalTime < $target) {
                $availableQuestions = array_keys($uniqueQuestions);
                $allQuestions = [];
                foreach ($data as $quiz) {
                    foreach ($quiz['quiz_image'] as $image) {
                        $allQuestions[$image['question_id']] = $image;
                    }
                }
                $allQuestions = array_diff_key($allQuestions, $uniqueQuestions);
                if (count($allQuestions) > 0) {
                    $randomKey = array_rand($allQuestions);
                    $additionalQuestion = $allQuestions[$randomKey];
                    foreach ($data as $quiz) {
                        if (array_search($additionalQuestion, $quiz['quiz_image']) !== false) {
                            $selectedQuizzes[] = $quiz;
                            break;
                        }
                    }
                }
            }

//            $result = [
//                'total_time' => $totalTime,
//                'quizzes' => $selectedQuizzes,
//            ];

            return $selectedQuizzes;


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
    )
    {
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
            $userId = Auth::user()->id;
            $questionId = $request->question_id;

            // Check if a record exists
            $question = Quiz::where('user_id', $userId)
                ->where('question_id', $questionId)
                ->first();

            if ($question) {
                // Update existing record with new values and add time taken
                $question->update([
                    'answer' => $request->response,
                    'time' => $question->time + $request->time_taken,
                    'quiz_id' => $request->quiz_id
                ]);
            } else {
                // Insert new record if none exists
                Quiz::create([
                    'answer' => $request->response,
                    'time' => $request->time_taken,
                    'user_id' => $userId,
                    'question_id' => $questionId,
                    'quiz_id' => $request->quiz_id
                ]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return response()->json(['success' => false]);
        }
    }

    public function addTime()
    {
        $time = Setting::first();
        $student = Auth::user()->std;

        $studentArray = json_decode($student, true);

        $allTopics = [];
        foreach ($studentArray as $data) {
            $topics = Topic::where('std', $data)->get();
            foreach ($topics as $topic) {
                $allTopics[$data][] = $topic->toArray();
            }
        }

        return view('student.addtime', compact('time', 'allTopics'));
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

            Question::where('id', $request->question_id)->update(['reported' => '1']);

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

    public function QuestionPreviousAns(Request $request)
    {
        try {
            $quizData = '';
            $html = '<ul class="nav nav-pills nav-sidebar flex-column progress-box" data-widget="treeview" role="menu" data-accordion="false">';

            foreach ($request->question as $index=>$question) {
                $questionId = $question['id'];
                $quizData = Quiz::where('user_id', Auth::user()->id)->where('question_id',$questionId)->where('quiz_id',$request->quiz_id)->first();
                if($quizData){
                    $class = 'answer-wrong';
                    if($quizData->answer == 'correct'){
                        $class = 'answer-correct';
                    }
                    $html .= '<li onclick="loadSkippedQuestion(['.($index).'])" class="nav-item">
                    <span class="progress-circle '.$class.'" id="item-' . $question['id'] . '">
                        <p>' . ($index + 1) . '</p>
                    </span>
                  </li>
                  <li>
                    <span class="progress-line"></span>
                  </li>';
                }else{
                    $html .= '<li onclick="loadSkippedQuestion(['.($index).'])" class="nav-item">
                    <span class="progress-circle" id="item-' . $question['id'] . '">
                        <p>' . ($index + 1) . '</p>
                    </span>
                  </li>
                  <li>
                    <span class="progress-line"></span>
                  </li>';
                }

            }
            $html .= '</ul>';
            return response()->json(['html' => $html]);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return response()->json(['success' => false]);
        }
    }

    public function closeQuiz($id)
    {
        try {
             Quiz::where('user_id', $id)->delete();
             Reported::where('user_id', $id)->delete();

            return redirect()->back()->with('success', 'All Previous Quiz data reset successfully');
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return response()->json(['success' => false]);
        }
    }


}
