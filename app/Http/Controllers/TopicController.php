<?php

namespace App\Http\Controllers;


use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class TopicController extends Controller
{
    public function index()
    {
        return view('topic.index');
    }

    public function getData(){
        try {
            $Topic = Topic::orderBy('created_at', 'desc')->get();
            $Topic->each(function ($item, $index) {
                $item->index = $index + 1;
            });
            return Datatables::of($Topic)
                ->addColumn('no', function ($row) {
                    return $row->index;
                })
                ->addColumn('std', function ($row) {
                    if ($row->std == 1) {
                        $ed = "st";
                    } elseif ($row->std == 2) {
                        $ed = "nd";
                    }elseif ($row->std == 3) {
                        $ed = "rd";
                    }else{
                        $ed = "th";
                    }
                    return  $row->std.'<sup>'.$ed.'</sup>';
                })
                ->addColumn('title', function ($row) {
                    return $row->title;
                })
                ->addColumn('actions', function ($row) {
                    $editButton = '<a href="' . route('topic.edit', $row->id) . '" class="btn btn-primary btn-sm edit-student" data-id="' . $row->id . '">Edit</a>';
                    $deleteButton = '<button class="btn btn-danger btn-sm delete-topic" data-id="' . $row->id . '">Delete</button>';
                    return $editButton . ' ' . $deleteButton;
                })
                ->rawColumns(['no', 'std', 'title', 'actions'])
                ->make(true);
        } catch (\Exception $e) {
            Log::info('In File : ' . $e->getFile() . ' - Line : ' . $e->getLine() . ' - Message : ' . $e->getMessage() . ' - At Time : ' . date('Y-m-d H:i:s'));
            return redirect()->back()->with('error', 'An error occurred. Please try again.');
        }
    }

    public function create()
    {
        return view('topic.create');
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'std' => 'required|numeric|max:255',
                'title' => 'required|max:255'
            ]);
            if ($validator->fails()) {
                return back()->withInput()->withErrors($validator);
            } else {
                $topic = new Topic();
                $topic->std = $request->std;
                $topic->title = $request->title;
                $topic->save();
                return redirect()->route('topic.index')->with('success', 'Topic created successfully.');
            }
        } catch (\Exception $e) {
            Log::info('In File : ' . $e->getFile() . ' - Line : ' . $e->getLine() . ' - Message : ' . $e->getMessage() . ' - At Time : ' . date('Y-m-d H:i:s'));
            return redirect()->back()->with('error', 'An error occurred. Please try again.');
        }
    }

    public function edit($id)
    {
        try {
            $data = Topic::find($id);
            return view('topic.edit', compact('data'));
        } catch (\Exception $e) {
            Log::info('In File : ' . $e->getFile() . ' - Line : ' . $e->getLine() . ' - Message : ' . $e->getMessage() . ' - At Time : ' . date('Y-m-d H:i:s'));
            return redirect()->back()->with('error', 'An error occurred. Please try again.');
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'std' => 'required|numeric|max:255',
            'title' => 'required|max:255'
        ]);
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        } else {
            $topic = Topic::findOrFail($id);
            $topic->std = $request->std;
            $topic->title = $request->title;
            $topic->save();
            return redirect()->route('topic.index')->with('success', 'Topic Update successfully.');
        }
    }

    public function destroy($id)
    {
        try {
            $school = Topic::findOrFail($id);
            $school->delete();
            return response()->json(['success' => 'Record deleted successfully']);
        } catch (\Exception $e) {
            Log::error('In File: ' . $e->getFile() . ' - Line: ' . $e->getLine() . ' - Message: ' . $e->getMessage() . ' - At Time: ' . now());
            return response()->json(['error' => 'Something went wrong!'], 500);
        }
    }
}
