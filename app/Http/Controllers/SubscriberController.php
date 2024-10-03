<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class SubscriberController extends Controller
{

    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $subscription = Subscription::with('user')->orderBy('created_at', 'desc');
                    if (!empty($request->filter)) {
                        if ($request->filter == 'active') {
                            $subscription->whereDate('end_date','>', now());
                        } else {
                            $subscription->whereDate('end_date','<', now());
                        }
                    }
                $subscription->get();

                $subscription->each(function ($item, $index) {
                    $item->index = $index + 1;
                });
                return Datatables::of($subscription)
                    ->addColumn('no', function ($row) {
                        return $row->index+1;
                    })
                    ->addColumn('subscriber', function ($row) {
                        return $row->user->email;
                    })
                    ->addColumn('amount', function ($row) {
                        return '$'.$row->amount;
                    })
                    ->addColumn('plan_start_date', function ($row) {
                        return $row->start_date;
                    })
                    ->addColumn('plan_end_date', function ($row) {
                        return $row->end_date;
                    })
                    ->addColumn('status', function ($row) {
                        $currentDate = now();
                        return $row->end_date < $currentDate ? '<span class="text-danger">Expired</span>' : '<span class="text-success">Active</span>';
                    })
                    ->rawColumns(['no', 'subscriber', 'plan_start_date','plan_end_date','status','amount'])
                    ->make(true);

            } else {
                return view('subscriber.index');
            }
        } catch (\Exception $e) {
            Log::info('In File : ' . $e->getFile() . ' - Line : ' . $e->getLine() . ' - Message : ' . $e->getMessage() . ' - At Time : ' . date('Y-m-d H:i:s'));
            return redirect()->back()->with('error', 'An error occurred. Please try again.');
        }
    }
}
