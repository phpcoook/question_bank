<?php

namespace App\Http\Controllers;

use App\Models\PaymentHistory;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Subscription;
use Yajra\DataTables\DataTables;


class PaymentController extends Controller
{

    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                if(Auth::user()->role == 'admin') {
                    $paymentHistory = PaymentHistory::with('user')->orderBy('created_at', 'desc');
                }else{
                    $paymentHistory = PaymentHistory::select('users.email as email','payment_history.*')->join('users','users.id','payment_history.user_id')->where('users.id',Auth::user()->id)->orderBy('payment_history.created_at', 'desc');
                }
                if (!empty($request->filter)) {
                    if ($request->filter == 'active') {
                        $paymentHistory->whereDate('end_date', '>', now());
                    } else {
                        $paymentHistory->whereDate('end_date', '<', now());
                    }
                }
                $paymentHistory->get();

                $paymentHistory->each(function ($item, $index) {
                    $item->index = $index + 1;
                });
                if(Auth::user()->role == 'admin') {
                    return Datatables::of($paymentHistory)
                        ->addColumn('no', function ($row) {
                            return $row->index + 1;
                        })
                        ->addColumn('subscriber', function ($row) {
                            return $row->user->email;
                        })
                        ->addColumn('plan_start_date', function ($row) {
                            return $row->start_date;
                        })
                        ->addColumn('plan_end_date', function ($row) {
                            return $row->end_date;
                        })
                        ->addColumn('amount', function ($row) {
                            return '$'.$row->amount;
                        })
                        ->addColumn('status', function ($row) {
                            $currentDate = now();
                            return $row->end_date < $currentDate ? '<span class="text-danger">Expired</span>' : '<span class="text-success">Active</span>';
                        })
                        ->addColumn('response', function ($row) {
                            return '<a href="javascript:;" class="btn btn-sm btn-primary show-response" data-id="' . $this->getConcateRes($row->customer_response,
                                    $row->payment_response) . '" ><i class="fa fa-eye"></i> Response</a>';
                        })
                        ->rawColumns([
                            'no',
                            'subscriber',
                            'plan_start_date',
                            'plan_end_date',
                            'amount',
                            'status',
                            'response'
                        ])
                        ->make(true);
                }else{
                    return Datatables::of($paymentHistory)
                        ->addColumn('no', function ($row) {
                            return $row->index + 1;
                        })
                        ->addColumn('subscriber', function ($row) {
                            return $row->email;
                        })
                        ->addColumn('plan_start_date', function ($row) {
                            return $row->start_date;
                        })
                        ->addColumn('plan_end_date', function ($row) {
                            return $row->end_date;
                        })
                        ->addColumn('amount', function ($row) {
                            return '$'.$row->amount;
                        })
                        ->addColumn('status', function ($row) {
                            $currentDate = now();
                            return $row->end_date < $currentDate ? '<span class="text-danger">Expired</span>' : '<span class="text-success">Active</span>';
                        })
                        ->rawColumns([
                            'no',
                            'subscriber',
                            'plan_start_date',
                            'plan_end_date',
                            'amount',
                            'status'
                        ])
                        ->make(true);
                }

            } else {
                return view('payment.index');
            }
        } catch (\Exception $e) {
            Log::info('In File : ' . $e->getFile() . ' - Line : ' . $e->getLine() . ' - Message : ' . $e->getMessage() . ' - At Time : ' . date('Y-m-d H:i:s'));
            return redirect()->back()->with('error', 'An error occurred. Please try again.');
        }
    }

    private function getConcateRes($data1,$data2){
        return base64_encode('<h3>Customer Response</h3>'.$this->getResponse($data1). '<h3>Payment Response</h3>'.$this->getResponse($data2));
    }

    private function getResponse($data) {
        $htmlTable = "<table border='1'>";
        $htmlTable .= "<tr><th>Response</th><th>Value</th></tr>";

        // Decode the JSON data
        $decodedData = json_decode($data);

        // Check if decoding was successful
        if (json_last_error() !== JSON_ERROR_NONE) {
            return "<p>Error decoding JSON: " . json_last_error_msg() . "</p>";
        }

        // Convert to an associative array
        $dataArray = (array) $decodedData;

        foreach ($dataArray as $key => $value) {
            // Handle nested array or object
            if (is_array($value) || is_object($value)) {
                foreach ((array) $value as $subKey => $subValue) {
                    // Use json_encode for both objects and arrays
                    $htmlTable .= "<tr><td>$subKey</td><td style='word-wrap:break-word; max-width:230px'>" . json_encode($subValue) . "</td></tr>";
                }
            } else {
                $htmlTable .= "<tr><td>$key</td><td>" . ($value ?? 'null') . "</td></tr>";
            }
        }

        $htmlTable .= "</table>";
        return $htmlTable;
    }


    public function processPayment(Request $request)
    {
        $request->validate([
            'payment_method_id' => 'required|string',
        ]);

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $paymentId = Setting::find(1);
            if (empty($paymentId)) {
                return response()->json(['success' => false, 'message' => 'Monthly Plan not Found'], 200);
            }
            $customer = Customer::create([
                'payment_method' => $request->payment_method_id,
                'email' => auth()->user()->email,
                'invoice_settings' => [
                    'default_payment_method' => $request->payment_method_id,
                ],
            ]);

            $subscription = Subscription::create([
                'customer' => $customer->id,
                'items' => [['price' => $paymentId->stripe_price_id]],
                'expand' => ['latest_invoice.payment_intent'],
            ]);

            $currentPeriodEnd = $subscription->current_period_end;
            $currentPeriodStart = $subscription->current_period_start;
            $formattedEnd = date('Y-m-d H:i:s', $currentPeriodEnd);
            $formattedStart = date('Y-m-d H:i:s', $currentPeriodStart);

            $localSubscription = new \App\Models\Subscription;
            $localSubscription->user_id = auth()->user()->id;
            $localSubscription->payment_status = 1;
            $localSubscription->amount = $paymentId->subscription_charge;
            $localSubscription->customer_response = json_encode([$customer], JSON_PRETTY_PRINT);
            $localSubscription->payment_response = json_encode([$subscription], JSON_PRETTY_PRINT);
            $localSubscription->start_date = $formattedStart;
            $localSubscription->end_date = $formattedEnd;
            $localSubscription->save();

            $localSubscription = new PaymentHistory();
            $localSubscription->user_id = auth()->user()->id;
            $localSubscription->payment_status = 1;
            $localSubscription->amount = $paymentId->subscription_charge;
            $localSubscription->customer_response = json_encode([$customer], JSON_PRETTY_PRINT);
            $localSubscription->payment_response = json_encode([$subscription], JSON_PRETTY_PRINT);
            $localSubscription->start_date = $formattedStart;
            $localSubscription->end_date = $formattedEnd;
            $localSubscription->save();

            return response()->json(['success' => true, 'subscription' => $subscription]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
