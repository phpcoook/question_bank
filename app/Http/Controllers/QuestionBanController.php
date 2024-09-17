<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\QuestionImage;
use Yajra\DataTables\DataTables;

class QuestionBanController extends Controller
{
    public function create()
    {
        return view('question.create');
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'difficulty' => 'required|in:foundation,intermediate,challenging',
                'question' => 'required|string|max:5000',
                'code' => 'required',
            ]);
            if ($validator->fails()) {
                return back()->withInput()->withErrors($validator);
            } else {

                $question = new Question();
                $question->code = $request->code;
                $question->difficulty = $request->difficulty;
                $question->question = $request->question;
                $question->answer = $request->answer;
                $question->save();

                if ($request->hasFile('image')) {
                    foreach ($request->file('image') as $image) {
                        $imageName = time() . '_' . $image->getClientOriginalName();

                        $image->storeAs('public/images', $imageName);

                        $questionImage = new QuestionImage();
                        $questionImage->question_id = $question->id;
                        $questionImage->image_name = $imageName;
                        $questionImage->save();
                    }
                }
                return redirect()->route('question.index')->with('success', 'Question created successfully.');
            }
        } catch (\Exception $e) {
            Log::info('In File : ' . $e->getFile() . ' - Line : ' . $e->getLine() . ' - Message : ' . $e->getMessage() . ' - At Time : ' . date('Y-m-d H:i:s'));
            return redirect()->back()->with('error', 'An error occurred. Please try again.');
        }
    }

    public function index()
    {
        return view('question.index');
    }

    public function getQuestionsData()
    {
        try {
            $questions = Question::all();
            return DataTables::of($questions)
                ->addIndexColumn()
                ->addColumn('actions', function ($question) {
                    $editButton = '<a href="' . route('question.edit', $question->id) . '" class="btn btn-primary btn-sm edit-question" data-id="' . $question->id . '">Edit</a>';
                    $deleteButton = '<button class="btn btn-danger btn-sm delete-question" data-id="' . $question->id . '">Delete</button>';
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
            $data = Question::find($id);
            $image = QuestionImage::where('question_id', $id)->get();
            return view('question.edit', compact('data', 'image'));
        } catch (\Exception $e) {
            Log::info('In File : ' . $e->getFile() . ' - Line : ' . $e->getLine() . ' - Message : ' . $e->getMessage() . ' - At Time : ' . date('Y-m-d H:i:s'));
            return redirect()->back()->with('error', 'An error occurred. Please try again.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            Log::info('$request');
            Log::info($request);
            $validator = Validator::make($request->all(), [
                'difficulty' => 'required|in:foundation,intermediate,challenging',
                'question' => 'required|string|max:5000',
                'code' => 'required',
            ]);
            if ($validator->fails()) {
                return back()->withInput()->withErrors($validator);
            } else {

                $question = Question::findOrFail($id);
                $question->difficulty = $request->difficulty;
                $question->question = $request->question;
                $question->answer = $request->answer;
                $question->code = $request->code;
                $question->save();

                // Handle image removal
                if ($request->remove_images) {
                    $removeImages = explode(',', $request->remove_images);
                    foreach ($removeImages as $imageId) {
                        $oldImage = QuestionImage::find($imageId);
                        if ($oldImage) {
                            $oldImagePath = storage_path('app/public/images/' . $oldImage->image_name);
                            if (file_exists($oldImagePath)) {
                                unlink($oldImagePath); // Remove image from storage
                            }
                            $oldImage->delete(); // Remove image record from database
                        }
                    }
                }

                // Handle new image uploads
                if ($request->hasFile('image')) {
                    foreach ($request->file('image') as $image) {
                        // Generate a unique file name
                        $imageName = time() . '_' . $image->getClientOriginalName();

                        // Store the image in the storage directory (you can adjust the path as needed)
                        $image->storeAs('public/images', $imageName);

                        // Create a new record in the question_images table
                        $questionImage = new QuestionImage();
                        $questionImage->question_id = $question->id; // Associate with the question
                        $questionImage->image_name = $imageName; // Store the file name
                        $questionImage->save(); // Save the image record
                    }
                }

                return redirect()->route('question.index')->with('success', 'Question Update successfully.');
            }

        } catch (\Exception $e) {
            Log::info('In File : ' . $e->getFile() . ' - Line : ' . $e->getLine() . ' - Message : ' . $e->getMessage() . ' - At Time : ' . date('Y-m-d H:i:s'));
            return redirect()->back()->with('error', 'An error occurred. Please try again.');
        }
    }

    public function destroy($id)
    {
        try {
            $question = Question::findOrFail($id);
            $questionImages = QuestionImage::where('question_id', $id)->get();
            foreach ($questionImages as $image) {
                $imagePath = public_path('uploads/questions/' . $image->image_name);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
                $image->delete();
            }
            $question->delete();
            return response()->json(['success' => 'Question and associated images deleted successfully']);
        } catch (\Exception $e) {
            Log::error('In File: ' . $e->getFile() . ' - Line: ' . $e->getLine() . ' - Message: ' . $e->getMessage() . ' - At Time: ' . now());
            return response()->json(['error' => 'Something went wrong!'], 500);
        }
    }
}
