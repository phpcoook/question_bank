<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\QuestionImage;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use App\Mail\Register;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function loginView()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        try {

            // Validate the login form data
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|min:8',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Check if the user exists and email is verified
            $user = User::where('email', $request->input('email'))->first();

            if ($user && $user->email_verified_at !== null) {
                if (Auth::attempt(['email' => $request->input('email'), 'password' => $request->input('password')])) {
                    $role = User::where('email',$request->email)->get();
                    if($role[0]['role'] == 'student'){
                        return redirect()->route('student.dashboard');
                    }elseif ($role[0]['role'] == 'tutor'){
                        return redirect()->route('tutor.dashboard');
                    }else{
                        return redirect()->route('admin.login');
                    }
                } else {
                    return redirect()->back()
                        ->withErrors(['password' => 'Invalid credentials'])
                        ->withInput();
                }
            } else {
                return redirect()->back()
                    ->withErrors(['email' => 'Email not verified or does not exist'])
                    ->withInput();
            }
        } catch (\Exception $e) {
            Log::info('In File : ' . $e->getFile() . ' - Line : ' . $e->getLine() . ' - Message : ' . $e->getMessage() . ' - At Time : ' . date('Y-m-d H:i:s'));
            return redirect()->back()->with('error', 'An error occurred. Please try again.');
        }
    }

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
                'grade' => 'nullable|string|max:255',
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
                $user->grade = $request->grade;
                $user->std = $request->std;
                $user->date_of_birth = $request->date_of_birth;
                $user->email_verified_at = '2024-09-18';
                $user->role = 'student';
                $user->save();

                // Send the registration email
                Mail::to($user->email)->send(new Register($user,$request->password));

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
                ->addColumn('actions', function ($student) {
                    $editButton = '<a href="' . route('student.edit', $student->id) . '" class="btn btn-primary btn-sm edit-student" data-id="' . $student->id . '">Edit</a>';
                    $deleteButton = '<button class="btn btn-danger btn-sm delete-student" data-id="' . $student->id . '">Delete</button>';
                    return $editButton . ' ' . $deleteButton;
                })
                ->rawColumns(['actions'])
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
            'grad' => 'nullable|string|max:255',
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
            $student->grade = $request->grade;
            $student->std = $request->std;
            $student->date_of_birth = $request->date_of_birth;
            if(!empty($request->password)){
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

    public function dashboard(){
        return view('student.dashboard');
    }

    public function wrongQuestion()
    {
        try {
            $wrong = Quiz::where('user_id', Auth::user()->id)->where('answer', 'wrong')->pluck('question_id');
            $questions = Question::with('quizImage')->wherein('id', $wrong)->get()->toArray();
            return view('student.wrongQuestion', compact('questions'));
        } catch (\Exception $e) {
            Log::error('In File: ' . $e->getFile() . ' - Line: ' . $e->getLine() . ' - Message: ' . $e->getMessage() . ' - At Time: ' . now());
            return response()->json(['error' => 'Something went wrong!'], 500);
        }
    }

}
