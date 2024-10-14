<?php

namespace App\Http\Controllers;

use App\Models\SubTopic;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class SubTopicController extends Controller
{
    public function index()
    {
        $topics = SubTopic::with('topics')->get();
        return view('sub-topic.index',compact('topics'));
    }

    public function getData(){
        try {
            $subTopic = SubTopic::with('topics')->orderBy('created_at', 'desc')->get();
            $subTopic->each(function ($item, $index) {
                $item->index = $index + 1;
            });

            return Datatables::of($subTopic)
                ->addColumn('no', function ($row) {
                    return $row->index;
                })
                ->addColumn('topic', function ($row) {
                    return  $row->topics->title;
                })
                ->addColumn('title', function ($row) {
                    return $row->title;
                })
                ->addColumn('actions', function ($row) {
                    $editButton = '<a href="' . route('sub-topic.edit', $row->id) . '" class="btn btn-primary btn-sm edit-student" data-id="' . $row->id . '">Edit</a>';
                    $deleteButton = '<button class="btn btn-danger btn-sm delete-sub-topic" data-id="' . $row->id . '">Delete</button>';
                    return $editButton . ' ' . $deleteButton;
                })
                ->rawColumns(['no', 'topic', 'title', 'actions'])
                ->make(true);
        } catch (\Exception $e) {
            Log::info('In File : ' . $e->getFile() . ' - Line : ' . $e->getLine() . ' - Message : ' . $e->getMessage() . ' - At Time : ' . date('Y-m-d H:i:s'));
            return redirect()->back()->with('error', 'An error occurred. Please try again.');
        }
    }

    public function getDataByIds(Request $request) {
        try {
            $topicIds = is_array($request->topic_ids) ? $request->topic_ids : [$request->topic_ids];
            $topicIds = array_unique(array_map('intval', $topicIds));
            $subTopics = SubTopic::whereIn('topic_id', $topicIds)->get();
            $html = '<option disabled value=""> Select SubTopics</option>';
            if ($subTopics->count()) {
                foreach ($subTopics as $subTopic) {
                    $html .= '<option value="' . $subTopic->id . '"> ' . $subTopic->title . '</option>';
                }
            }
            return response()->json(['data' => $html]);
        } catch (\Exception $e) {
            Log::info('In File : ' . $e->getFile() . ' - Line : ' . $e->getLine() . ' - Message : ' . $e->getMessage() . ' - At Time : ' . date('Y-m-d H:i:s'));
            return redirect()->back()->with('error', 'An error occurred. Please try again.');
        }
    }

    public function getTopics(Request $request)
    {
        try {
            $data = Topic::where('std',$request->std)->get();
            $html = '<option disabled value=""> Select SubTopics</option>';
            if ($data->count()) {
                foreach ($data as $Topic) {
                    $html .= '<option value="' . $Topic->id . '"> ' . $Topic->title . '</option>';
                }
            }
            return response()->json(['data' => $html]);
        } catch (\Exception $e) {
            Log::info('In File : ' . $e->getFile() . ' - Line : ' . $e->getLine() . ' - Message : ' . $e->getMessage() . ' - At Time : ' . date('Y-m-d H:i:s'));
            return redirect()->back()->with('error', 'An error occurred. Please try again.');
        }
    }

    public function getSelectedDataByIds(Request $request) {
        try {
            $topicIds = is_array($request->topic_ids) ? $request->topic_ids : [$request->topic_ids];
            $topicIds = array_unique(array_map('intval', $topicIds));
            $subTopics = SubTopic::whereIn('topic_id', $topicIds)->get();
            $html = '<option disabled value=""> Select SubTopics</option>';

            $array = json_decode(html_entity_decode($request->selected), true);

            if ($subTopics->count()) {
                foreach ($subTopics as $subTopic) {
                    $selected = (in_array($subTopic->id,$array))?'selected':'';
                    $html .= '<option '.$selected.' value="' . $subTopic->id . '"> ' . $subTopic->title . '</option>';
                }
            }
            return response()->json(['data' => $html]);
        } catch (\Exception $e) {
            Log::info('In File : ' . $e->getFile() . ' - Line : ' . $e->getLine() . ' - Message : ' . $e->getMessage() . ' - At Time : ' . date('Y-m-d H:i:s'));
            return redirect()->back()->with('error', 'An error occurred. Please try again.');
        }
    }



    public function create()
    {
        $topics = Topic::all();
        return view('sub-topic.create',compact('topics'));
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'topic_id' => 'required',
                'title' => 'required|max:255'
            ]);
            if ($validator->fails()) {
                return back()->withInput()->withErrors($validator);
            } else {
                $topic = new SubTopic();
                $topic->topic_id = $request->topic_id;
                $topic->title = $request->title;
                $topic->save();
                return redirect()->route('sub-topic.index')->with('success', 'Sub Topic created successfully.');
            }
        } catch (\Exception $e) {
            Log::info('In File : ' . $e->getFile() . ' - Line : ' . $e->getLine() . ' - Message : ' . $e->getMessage() . ' - At Time : ' . date('Y-m-d H:i:s'));
            return redirect()->back()->with('error', 'An error occurred. Please try again.');
        }
    }

    public function edit($id)
    {
        try {
            $topics = Topic::all();
            $data = SubTopic::find($id);
            return view('sub-topic.edit', compact('data','topics'));
        } catch (\Exception $e) {
            Log::info('In File : ' . $e->getFile() . ' - Line : ' . $e->getLine() . ' - Message : ' . $e->getMessage() . ' - At Time : ' . date('Y-m-d H:i:s'));
            return redirect()->back()->with('error', 'An error occurred. Please try again.');
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'topic_id' => 'required',
            'title' => 'required|max:255'
        ]);
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        } else {
            $topic = SubTopic::findOrFail($id);
            $topic->topic_id = $request->topic_id;
            $topic->title = $request->title;
            $topic->save();
            return redirect()->route('sub-topic.index')->with('success', 'Sub Topic Update successfully.');
        }
    }

    public function destroy($id)
    {
        try {
            $school = SubTopic::findOrFail($id);
            $school->delete();
            return response()->json(['success' => 'Record deleted successfully']);
        } catch (\Exception $e) {
            Log::error('In File: ' . $e->getFile() . ' - Line: ' . $e->getLine() . ' - Message: ' . $e->getMessage() . ' - At Time: ' . now());
            return response()->json(['error' => 'Something went wrong!'], 500);
        }
    }
}
