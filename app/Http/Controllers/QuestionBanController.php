<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Quiz;
use App\Models\Reported;
use App\Models\Topic;
use App\Rules\SubTopicsRequired;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\QuestionImage;
use Yajra\DataTables\DataTables;

class QuestionBanController extends Controller
{
    public function create()
    {
        $topics = Topic::all();
        return view('question.create', compact('topics'));
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
                'topics' => 'required|array', // Ensure topics are required
                'sub_topics' => [
                    'required',
                    'array',
                    new SubTopicsRequired($request->input('topics')),
                ],
                'std' => 'required',
            ]);

            if ($validator->fails()) {
                return back()->withInput()->withErrors($validator);
            } else {

                $question = new Question();
                $question->code = $request->code;
                $question->difficulty = $request->difficulty;
                $question->time = $request->time;
                $question->topic_id = json_encode($request->topics,1);
                $question->subtopic_id = json_encode($request->sub_topics,1);
                $question->std = $request->std;
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
        $reported = Question::where('reported', '1')->count();
        return view('question.index', compact('reported'));
    }

    public function getQuestionsData(Request $request)
    {
        try {
            $questions = Question::orderBy('created_at', 'desc');
            if (!empty($request->filter)) {
                if ($request->filter == 'reported') {
                    $questions->where('reported', '1');
                } else {
                    $questions->where('difficulty', $request->filter);
                }
            }
            $questions->get();
            return DataTables::of($questions)
                ->addIndexColumn()
                ->addColumn('actions', function ($question) {
                    $reported = ($question->reported) ? "<span class='text-danger px-3 text-bold'>Reported</span>" : '';
                    $editButton = '<a href="' . route('question.edit', $question->id) . '" class="btn btn-primary btn-sm edit-question" data-id="' . $question->id . '">Edit</a>';
                    $deleteButton = '<button class="btn btn-danger btn-sm delete-question" data-id="' . $question->id . '">Delete</button>' . $reported;
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
            $topics = Topic::all();
            $data = Question::find($id);
            $images = QuestionImage::where('question_id', $id)->get();
            return view('question.edit', compact('data', 'images', 'topics'));
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
                'topics' => 'required|array', // Ensure topics are required
                'sub_topics' => [
                    'required',
                    'array',
                    new SubTopicsRequired($request->input('topics')),
                ],
                'std' => 'required',
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
                $question->topic_id = json_encode($request->topics,1);
                $question->subtopic_id = json_encode($request->sub_topics,1);
                $question->std = $request->std;
                $question->save();

                // Handle question images removal
                if ($request->remove_question_images) {
                    $removeImages = explode(',', $request->remove_question_images);
                    foreach ($removeImages as $imageId) {
                        $oldImage = QuestionImage::find($imageId);
                        if ($oldImage) {
                            $oldImagePath = storage_path('app/public/images/' . $oldImage->image_name);
                            if (file_exists($oldImagePath)) {
                                unlink($oldImagePath);
                            }
                            $oldImage->delete();
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
            $quizRecords = Quiz::where('question_id', $id)->get();
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

    public function report(Request $request)
    {
        try {
            if ($request->ajax()) {
                $report = Reported::with('user')->get();

                return DataTables::of($report)
                    ->addIndexColumn()
                    ->addColumn('user_name', function ($report) {
                        return $report->user->first_name.' '.$report->user->last_name;
                    })
                    ->addColumn('actions', function ($report) {
                        $editButton = '<a href="' . route('question.edit', $report->question_id) . '" class="btn btn-primary btn-sm edit-question" data-id="' . $report->question_id . '">Edit</a>';
                        return $editButton;
                    })
                    ->rawColumns(['actions'])
                    ->make(true);
            } else {
                return view('question.report');
            }


        } catch (\Exception $e) {
            Log::info('In File : ' . $e->getFile() . ' - Line : ' . $e->getLine() . ' - Message : ' . $e->getMessage() . ' - At Time : ' . date('Y-m-d H:i:s'));
            return redirect()->back()->with('error', 'An error occurred. Please try again.');
        }
    }
}
