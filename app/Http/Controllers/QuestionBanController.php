<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Quiz;
use App\Models\Reported;
use App\Models\SubTopic;
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
                'code' => 'required|unique:question,code',
                'time' => 'required|numeric|min:0',
                'questionimage' => 'required|array',
                'questionimage.*' => 'image|mimes:jpg,jpeg,png,gif,webp|max:2048',
                'topics' => 'required|array', // Ensure topics are required
                'std' => 'required',
            ]);

            if ($validator->fails()) {
                return back()->withInput()->withErrors($validator);
            } else {

                $question = new Question();
                $question->code = $request->code;
                $question->time = $request->time * 60;
                $question->topic_id = json_encode($request->topics, 1);
                $question->subtopic_id = json_encode($request->sub_topics, 1);
                $question->std = json_encode($request->std, 1);
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

                // Handle solution images
                if ($request->hasFile('solutionimage')) {
                    foreach ($request->file('solutionimage') as $image) {
                        $imageName = time() . '_' . $image->getClientOriginalName();
                        $image->storeAs('public/images', $imageName);

                        $questionImage = new QuestionImage();
                        $questionImage->question_id = $question->id;
                        $questionImage->image_name = $imageName;
                        $questionImage->type = 'solution'; // Mark it as an answer image
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
                        $questionImage->type = 'answer';
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

            $questions = Question::select(
                'id',
                'code',
                'time',
                'reported',
                'std',
                'subtopic_id',
                'topic_id'
            );
            if (!empty($request->filter)) {
                if ($request->filter == 'reported') {
                    $questions->where('reported', '1');
                }
            }
            $questions = $questions->get()->toArray();
            $subtopicIds = [];
            $topicIds = [];
            foreach ($questions as $question) {
                $subtopicIds = array_merge($subtopicIds, json_decode($question['subtopic_id'] ?? '[]', true) ?: []);
                $topicIds = array_merge($topicIds, json_decode($question['topic_id'] ?? '[]', true) ?: []);
            }
            $subtopicIds = array_unique($subtopicIds);
            $topicIds = array_unique($topicIds);
            $subtopics = SubTopic::whereIn('id', $subtopicIds)->pluck('title', 'id')->toArray();
            $topics = Topic::whereIn('id', $topicIds)->pluck('title', 'id')->toArray();
            foreach ($questions as &$question) {
                $subtopicArray = json_decode($question['subtopic_id'], true);
                $topicArray = json_decode($question['topic_id'], true);

                if (is_array($subtopicArray)) {
                    $question['subtopic_name'] = array_map(function ($id) use ($subtopics) {
                        return !empty($subtopics[$id]) ? '<small class="badge badge-primary" >' . $subtopics[$id] . '</small>' : null;
                    }, $subtopicArray);
                } else {
                    $question['subtopic_name'] = ['name' => '<span class="badge badge-secondary">N/A</span>'];
                }

                if (is_array($topicArray)) {
                    $question['topic_name'] = array_map(function ($id) use ($topics) {
                        return !empty($topics[$id]) ? '<small class="badge badge-primary" >' . $topics[$id] . '</small>' : null;
                    }, $topicArray);
                } else {
                    $question['topic_name'] = ['name' => '<span class="badge badge-secondary">N/A</span>'];
                }
            }


            return DataTables::of($questions)
                ->addIndexColumn()
                ->addColumn('code', function ($row) {
                    return $row['code'];
                })
                ->addColumn('std', function ($row) {
                    $std = trim($row['std'], '[]');
                    $stdArray = explode(',', $std);
                    $cleanedStd = array_map(function ($item) {
                        return str_replace('_', ' ', trim($item, '"'));
                    }, $stdArray);

                    $badgeHtml = '';
                    foreach ($cleanedStd as $item) {
                        $badgeHtml .= '<small class="badge badge-primary">Year ' . $item . '</small> ';
                    }
                    return $badgeHtml;
                })
                ->addColumn('subtopic_name', function ($row) {
                    $sub_html = "";
                    foreach ($row['subtopic_name'] as $items) {
                        $sub_html .= $items . " ";
                    }
                    return trim($sub_html);
                })
                ->addColumn('topic_name', function ($row) {
                    $html = "";
                    foreach ($row['topic_name'] as $item) {
                        $html .= $item . " ";
                    }
                    return trim($html);
                })
                ->addColumn('actions', function ($row) {
                    $reported = ($row['reported']) ? "<span class='text-danger px-3 text-bold'>Reported</span>" : '';
                    $editButton = '<a href="' . route('question.edit',
                            $row['id']) . '" class="btn btn-primary btn-sm edit-question" data-id="' . $row['id'] . '">Edit</a>';
                    $deleteButton = '<button class="btn btn-danger btn-sm delete-question" data-id="' . $row['id'] . '">Delete</button>' . $reported;
                    return $editButton . ' ' . $deleteButton;
                })
                ->rawColumns(['actions', 'code', 'std', 'subtopic_name', 'topic_name'])
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
                'code' => 'required',
                'time' => 'required|numeric|min:0',
                'topics' => 'required|array', // Ensure topics are required
                'std' => 'required',
            ]);
            if ($validator->fails()) {
                return back()->withInput()->withErrors($validator);
            } else {

                $question = Question::findOrFail($id);
                $question->code = $request->code;
                $question->time = $request->time * 60;
                $question->topic_id = json_encode($request->topics, 1);
                $question->subtopic_id = json_encode($request->sub_topics, 1);
                $question->std = json_encode($request->std, 1);
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


                // Handle solution images removal
                if ($request->remove_solution_images) {
                    $removeImages = explode(',', $request->remove_solution_images);
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
                if ($request->hasFile('solutionimage')) {
                    foreach ($request->file('solutionimage') as $image) {
                        $imageName = time() . '_' . $image->getClientOriginalName();
                        $image->storeAs('public/images', $imageName);

                        $questionImage = new QuestionImage();
                        $questionImage->question_id = $question->id;
                        $questionImage->image_name = $imageName;
                        $questionImage->type = 'solution'; // Mark it as an answer image
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
                        $questionImage->type = 'answer';
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
                        return $report->user->first_name . ' ' . $report->user->last_name;
                    })
                    ->addColumn('actions', function ($report) {
                        $editButton = '<a href="' . route('question.edit',
                                $report->question_id) . '" class="btn btn-primary btn-sm edit-question" data-id="' . $report->question_id . '">Edit</a>';
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
