<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Quiz;
use App\Models\Setting;
use App\Models\Topic;
use App\Models\Pricing;
use App\Models\Subscription;
use App\Models\User;
use App\Services\CustomService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use App\Mail\Register;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{

    public function create()
    {
        return view('student.create');
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8',
                'date_of_birth' => 'required|date',
                'std' => 'required',
            ]);
            if ($validator->fails()) {
                return back()->withInput()->withErrors($validator);
            } else {
                $user = new User();
                $user->first_name = $request->first_name;
                $user->last_name = $request->last_name;
                $user->email = $request->email;
                $user->password = Hash::make($request->password);
                $user->std = json_encode($request->std, 1);
                $user->date_of_birth = $request->date_of_birth;
                $user->email_verified_at = '2024-09-18';
                $user->role = 'student';
                $user->save();

                // Send the registration email
                Mail::to($user->email)->send(new Register($user, $request->password));

                return redirect()->route('student.index')->with('success', 'Student created successfully.');
            }
        } catch (\Exception $e) {
            Log::info('In File : ' . $e->getFile() . ' - Line : ' . $e->getLine() . ' - Message : ' . $e->getMessage() . ' - At Time : ' . date('Y-m-d H:i:s'));
            return redirect()->back()->with('error', 'An error occurred. Please try again.');
        }
    }

    public function index()
    {
        return view('student.index');
    }

    public function getStudentData()
    {
        try {
            $student = User::where('role', 'student')->orderBy('created_at', 'desc')->get();
            return DataTables::of($student)
                ->addIndexColumn()
                ->addColumn('std', function ($row) {
                    $std = trim($row['std'], '[]');
                    $stdArray = explode(',', $std);
                    $cleanedStd = array_map(function ($item) {
                        return trim($item, '"');
                    }, $stdArray);

                    $badgeHtml = '';
                    foreach ($cleanedStd as $item) {
                        $formattedItem = str_replace('_', ' ', $item);
                        $badgeHtml .= '<small class="badge badge-primary">' . $formattedItem . '</small> ';
                    }
                    return $badgeHtml;
                })
                ->addColumn('actions', function ($student) {
                    $editButton = '<a href="' . route('student.edit',
                            $student->id) . '" class="btn btn-primary btn-sm edit-student" data-id="' . $student->id . '">Edit</a>';
                    $deleteButton = '<button class="btn btn-danger btn-sm delete-student" data-id="' . $student->id . '">Delete</button>';
                    return $editButton . ' ' . $deleteButton;
                })
                ->addColumn('subscription', function ($student) {
                    $checked = $student->subscription_status ? 'checked' : '';
                    return ' <div class="custom-control custom-switch">
                        <input ' . $checked . ' type="checkbox" data-id="' . $student->id . '" class="custom-control-input " id="customSwitch' . $student->id . '">
                        <label class="custom-control-label" for="customSwitch' . $student->id . '"></label>
                        </div>';
                })
                ->rawColumns(['actions', 'subscription','std'])
                ->make(true);
        } catch (\Exception $e) {
            Log::info('In File : ' . $e->getFile() . ' - Line : ' . $e->getLine() . ' - Message : ' . $e->getMessage() . ' - At Time : ' . date('Y-m-d H:i:s'));
            return redirect()->back()->with('error', 'An error occurred. Please try again.');
        }
    }

    public function edit($id)
    {
        try {
            $data = User::find($id);
            return view('student.edit', compact('data'));
        } catch (\Exception $e) {
            Log::info('In File : ' . $e->getFile() . ' - Line : ' . $e->getLine() . ' - Message : ' . $e->getMessage() . ' - At Time : ' . date('Y-m-d H:i:s'));
            return redirect()->back()->with('error', 'An error occurred. Please try again.');
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
//            'email' => 'required|email|unique:users,email,' . $id,
//            'password' => 'required|string|min:8',
            'date_of_birth' => 'required|date',
            'std' => 'required',
        ]);
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        } else {
            $student = User::findOrFail($id);

            // Check if email or password is changed
            $emailChanged = $student->email !== $request->email;

            $student->first_name = $request->first_name;
            $student->last_name = $request->last_name;
            $student->email = $request->email;
            $student->std = $request->std;
            $student->date_of_birth = $request->date_of_birth;
            if (!empty($request->password)) {
                $student->password = Hash::make($request->password);
            }
            $student->save();

            // Send email notification if email or password is updated
//            if (!empty($emailChanged)) {
//                Mail::to($request->email)->send(new Register($student,$request->password));
//            }

            return redirect()->route('student.index')->with('success', 'Student Update successfully.');
        }
    }

    public function destroy($id)
    {
        try {
            $school = User::findOrFail($id);
            $school->delete();
            return response()->json(['success' => 'Record deleted successfully']);
        } catch (\Exception $e) {
            Log::error('In File: ' . $e->getFile() . ' - Line: ' . $e->getLine() . ' - Message: ' . $e->getMessage() . ' - At Time: ' . now());
            return response()->json(['error' => 'Something went wrong!'], 500);
        }
    }

    public function dashboard()
    {
        $std = Auth::user()->std ?? 1;
        $userId = Auth::user()->id;

        $studentArray = json_decode($std, true);

        $topics = [];
        foreach ($studentArray as $data) {
            $queriedTopics = Topic::where('std', $data)->get();
            $topics = array_merge($topics, $queriedTopics->toArray());
        }

        $questions = Question::where(function ($query) use ($topics) {
            foreach ($topics as $topic) {
                $query->orWhereRaw('JSON_CONTAINS(topic_id, ?)', [json_encode((string)$topic['id'])]);
            }
        })->where('reported', '0')->pluck('id');

        $topicData = collect($topics)->map(function ($topic) use ($questions, $userId) {
            $totalQuestions = Question::whereRaw('JSON_CONTAINS(topic_id, ?)',
                [json_encode((string)$topic['id'])])->where('reported', '0')->count();
            $attemptedQuestions = Quiz::whereIn('question_id', $questions)
                ->where('user_id', $userId)
                ->whereIn('question_id',
                    Question::whereRaw('JSON_CONTAINS(topic_id, ?)', [json_encode((string)$topic['id'])])->pluck('id'))
                ->count();

            return [
                'id' => $topic['id'],
                'title' => $topic['title'],
                'total_questions' => $totalQuestions,
                'attempted_questions' => $attemptedQuestions,
            ];
        });

        $subscription = Subscription::where('user_id', Auth::user()->id)->whereDate('end_date', '>', now())->first();
        $setting = Setting::find(1);
        $subscriptionStatus = CustomService::checkSubscription();

        return view('student.dashboard',
            compact('topicData', 'subscription', 'setting', 'subscriptionStatus'));
    }

    public function wrongQuestion(Request $request)
    {
        try {
            $all = Quiz::select('quiz_id', DB::raw('count(*) as count'))
                ->where('answer', 'wrong')
                ->where('user_id', Auth::user()->id)
                ->groupBy('quiz_id')
                ->get();
            if (!empty($request->quiz_id)) {
                $wrong = Quiz::where('user_id', Auth::user()->id)->where('quiz_id', $request->quiz_id)->where('answer',
                    'wrong')->pluck('question_id');

            } else {
                $wrong = Quiz::where('user_id', Auth::user()->id)->where('answer', 'wrong')->pluck('question_id');
            }
            $questions = Question::with('quizImage')->whereIn('id', $wrong)->paginate(1);
            return view('student.wrongQuestion', compact('questions', 'all'));
        } catch (\Exception $e) {
            Log::error('In File: ' . $e->getFile() . ' - Line: ' . $e->getLine() . ' - Message: ' . $e->getMessage() . ' - At Time: ' . now());
            return response()->json(['error' => 'Something went wrong!'], 500);
        }
    }

    public function previousQuiz(Request $request)
    {
        try {
            if ($request->ajax()) {
                $Topic = Quiz::select('quiz_id',
                    DB::raw('COUNT(*) as total_answers'),
                    DB::raw('SUM(CASE WHEN answer = "wrong" THEN 1 ELSE 0 END) as wrong_answers'),
                    DB::raw('SUM(CASE WHEN answer = "correct" THEN 1 ELSE 0 END) as correct_answers'),
                    DB::raw('ROUND((SUM(CASE WHEN answer = "correct" THEN 1 ELSE 0 END) / COUNT(*)) * 100, 0) as correct_percentage'),
                    DB::raw('DATE_FORMAT(MIN(created_at), "%Y-%m-%d") as created_at')
                )
                    ->where('user_id', Auth::user()->id)
                    ->groupBy('quiz_id')
                    ->get();


                $Topic->each(function ($item, $index) {
                    $item->index = $index + 1;
                });
                return Datatables::of($Topic)
                    ->addColumn('no', function ($row) {
                        return $row->index;
                    })
                    ->addColumn('quiz_id', function ($row) {
                        return $row->quiz_id;
                    })
                    ->addColumn('total_question', function ($row) {
                        return $row->total_answers;
                    })
                    ->addColumn('correct_answers', function ($row) {
                        return $row->correct_answers;
                    })
                    ->addColumn('wrong_answers', function ($row) {
                        return $row->wrong_answers;
                    })
                    ->addColumn('percentage', function ($row) {
                        return $row->correct_percentage . '%';
                    })
                    ->addColumn('quiz_date', function ($row) {
                        return date('jS M-Y', strtotime($row->created_at));
                    })
                    ->addColumn('quiz_date', function ($row) {
                        return date('jS M-Y', strtotime($row->created_at));
                    })
                    ->addColumn('actions', function ($row) {
                        return " <a class='btn btn-sm btn-info' href='" . url('student/wrong/question') . '/' . $row->quiz_id . "'><i class='fa fa-eye me-1'></i>  Wrong Ans</a>
                             ";
                    })
                    ->rawColumns([
                        'no',
                        'quiz_id',
                        'quiz_date',
                        'total_question',
                        'correct_answers',
                        'wrong_answers',
                        'percentage',
                        'actions'
                    ])
                    ->make(true);
            } else {
                return view('student.previous-quiz');
            }
        } catch (\Exception $e) {
            Log::info('In File : ' . $e->getFile() . ' - Line : ' . $e->getLine() . ' - Message : ' . $e->getMessage() . ' - At Time : ' . date('Y-m-d H:i:s'));
            return redirect()->back()->with('error', 'An error occurred. Please try again.');
        }
    }

    public function updateSubscription($id)
    {
        $user = User::where('id', $id)->first();
        $data = '1';
        if ($user->subscription_status) {
            $data = '0';
        }
        $student = User::findOrFail($id);
        $student->subscription_status = $data;
        $student->save();
        return true;
    }

}
