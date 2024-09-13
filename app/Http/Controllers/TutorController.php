<?php

namespace App\Http\Controllers;

use App\Models\Tutor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class TutorController extends Controller
{
    public function create()
    {
        return view('tutor.create');
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:students,email',
                'password' => 'required|string|min:8',
                'grad' => 'nullable|string|max:255',
                'date_of_birth' => 'required|date',
            ]);
            if ($validator->fails()) {
                return back()->withInput()->withErrors($validator);
            } else {

                $tutor = new Tutor();
                $tutor->first_name = $request->first_name;
                $tutor->last_name = $request->last_name;
                $tutor->email = $request->email;
                $tutor->password = bcrypt($request->password);
                $tutor->grad = $request->grad;
                $tutor->birthdate = $request->date_of_birth;
                $tutor->save();

                return redirect()->route('tutor.index')->with('success', 'Tutor created successfully.');
            }
        } catch (\Exception $e) {
            Log::info('In File : ' . $e->getFile() . ' - Line : ' . $e->getLine() . ' - Message : ' . $e->getMessage() . ' - At Time : ' . date('Y-m-d H:i:s'));
            return redirect()->back()->with('error', 'An error occurred. Please try again.');
        }
    }

    public function index()
    {
        return view('tutor.index');
    }

    public function getTutorData()
    {
        try {
            $tutor = Tutor::all();
            return DataTables::of($tutor)
                ->addIndexColumn()
                ->addColumn('actions', function ($tutor) {
                    $editButton = '<a href="' . route('tutor.edit', $tutor->id) . '" class="btn btn-primary btn-sm edit-tutor" data-id="' . $tutor->id . '">Edit</a>';
                    $deleteButton = '<button class="btn btn-danger btn-sm delete-tutor" data-id="' . $tutor->id . '">Delete</button>';
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
            $data = Tutor::find($id);
            return view('tutor.edit', compact('data'));
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
            $tutor = Tutor::findOrFail($id);
            $tutor->first_name = $request->first_name;
            $tutor->last_name = $request->last_name;
            $tutor->email = $request->email;
            if (!empty($request->password)) {
                $tutor->password = bcrypt($request->password);
            }
            $tutor->grad = $request->grad;
            $tutor->birthdate = $request->date_of_birth;
            $tutor->save();

            return redirect()->route('tutor.index')->with('success', 'Tutor Update successfully.');
        }
    }

    public function destroy($id)
    {
        try {
            $tutor = Tutor::findOrFail($id);
            $tutor->delete();
            return response()->json(['success' => 'Record deleted successfully']);
        } catch (\Exception $e) {
            Log::error('In File: ' . $e->getFile() . ' - Line: ' . $e->getLine() . ' - Message: ' . $e->getMessage() . ' - At Time: ' . now());
            return response()->json(['error' => 'Something went wrong!'], 500);
        }
    }
}
