<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

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
                'grade' => 'nullable|string|max:255',
                'date_of_birth' => 'required|date',
            ]);
            if ($validator->fails()) {
                return back()->withInput()->withErrors($validator);
            } else {
                $user = new User();
                $user->first_name = $request->first_name;
                $user->last_name = $request->last_name;
                $user->email = $request->email;
                $user->password = bcrypt($request->password);
                $user->grade = $request->grade;
                $user->date_of_birth = $request->date_of_birth;
                $user->role = 'student';
                $user->save();

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
            $student = User::where('role', 'student')->get();
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
            'email' => 'required|email',
//            'password' => 'required|string|min:8',
            'grad' => 'nullable|string|max:255',
            'date_of_birth' => 'required|date',
        ]);
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        } else {
            $student = User::findOrFail($id);
            $student->first_name = $request->first_name;
            $student->last_name = $request->last_name;
            $student->email = $request->email;
            if (!empty($request->password)) {
                $student->password = bcrypt($request->password);
            }
            $student->grade = $request->grade;
            $student->date_of_birth = $request->date_of_birth;
            $student->save();

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
}
