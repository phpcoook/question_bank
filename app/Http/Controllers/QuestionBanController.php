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
                'code' => 'required|unique:question,code',
                'time' => 'required|integer',
                'questionimage' => 'required|array',
                'questionimage.*' => 'image|mimes:jpg,jpeg,png,gif|max:2048',
            ]);
            if ($validator->fails()) {
                return back()->withInput()->withErrors($validator);
            } else {

                $question = new Question();
                $question->code = $request->code;
                $question->difficulty = $request->difficulty;
                $question->time = $request->time;
                $question->save();

                // Handle question images
                if ($request->hasFile('questionimage')) {
                    foreach ($request->file('questionimage') as $image) {
                        $imageName = time() . '_' . $image->getClientOriginalName();
                        $image->storeAs('public/images', $imageName);

                        $questionImage = new QuestionImage();
                        $questionImage->question_id = $question->id;
                        $questionImage->image_name = $imageName;
                        $questionImage->type = 'question'; // Mark it as a question image
                        $questionImage->save();
                    }
                }

                // Handle answer images
                if ($request->hasFile('answerimage')) {
                    foreach ($request->file('answerimage') as $image) {
                        $imageName = time() . '_' . $image->getClientOriginalName();
                        $image->storeAs('public/images', $imageName);

                        $questionImage = new QuestionImage();
                        $questionImage->question_id = $question->id;
                        $questionImage->image_name = $imageName;
                        $questionImage->type = 'answer'; // Mark it as an answer image
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
            $questions = Question::orderBy('created_at', 'desc')->get();
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
            $images = QuestionImage::where('question_id', $id)->get();
            return view('question.edit', compact('data', 'images'));
        } catch (\Exception $e) {
            Log::info('In File : ' . $e->getFile() . ' - Line : ' . $e->getLine() . ' - Message : ' . $e->getMessage() . ' - At Time : ' . date('Y-m-d H:i:s'));
            return redirect()->back()->with('error', 'An error occurred. Please try again.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'difficulty' => 'required|in:foundation,intermediate,challenging',
                'code' => 'required',
                'time' => 'required|integer',
            ]);
            if ($validator->fails()) {
                return back()->withInput()->withErrors($validator);
            } else {

                if (!$request->hasFile('questionimage') && empty($request->existing_question_images)) {
                    $validator->errors()->add('question_images', 'At least one question image is required!');
                    return redirect()->back()->withErrors($validator)->withInput();
                }

                $question = Question::findOrFail($id);
                $question->difficulty = $request->difficulty;
                $question->code = $request->code;
                $question->time = $request->time;
                $question->save();

                // Handle question images removal
                if ($request->remove_question_images) {
                    $removeImages = explode(',', $request->remove_question_images);
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


                // Handle answer images removal
                if ($request->remove_answer_images) {
                    $removeImages = explode(',', $request->remove_answer_images);
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

                // Handle question images
                if ($request->hasFile('questionimage')) {
                    foreach ($request->file('questionimage') as $image) {
                        $imageName = time() . '_' . $image->getClientOriginalName();
                        $image->storeAs('public/images', $imageName);

                        $questionImage = new QuestionImage();
                        $questionImage->question_id = $question->id;
                        $questionImage->image_name = $imageName;
                        $questionImage->type = 'question'; // Mark it as a question image
                        $questionImage->save();
                    }
                }

                // Handle answer images
                if ($request->hasFile('answerimage')) {
                    foreach ($request->file('answerimage') as $image) {
                        $imageName = time() . '_' . $image->getClientOriginalName();
                        $image->storeAs('public/images', $imageName);

                        $questionImage = new QuestionImage();
                        $questionImage->question_id = $question->id;
                        $questionImage->image_name = $imageName;
                        $questionImage->type = 'answer'; // Mark it as an answer image
                        $questionImage->save();
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
            $quizRecords = Quiz::where('question_id',$id)->get();
            foreach ($quizRecords as $quiz) {
                $quiz->delete();
            }

            $question = Question::findOrFail($id);
            $questionImages = QuestionImage::where('question_id', $id)->get();
            foreach ($questionImages as $image) {
                $imagePath = storage_path('app/public/images/' . $image->image_name);
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
