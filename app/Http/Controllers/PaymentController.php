<?php

namespace App\Http\Controllers;

use App\Models\PaymentHistory;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session;
use Stripe\Exception\ExceptionInterface;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Subscription;
use Stripe\Webhook;
use Yajra\DataTables\DataTables;


class PaymentController extends Controller
{
    protected $paymentId;

    public function __construct()
    {
        $this->paymentId = Setting::find(1);
        if (empty($this->paymentId)) {
            return redirect()->back()->with('error','Monthly Plan not Found');
        }
    }

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


    //New redirect Method

    public function createCheckoutSession(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price' => $this->paymentId->stripe_price_id,
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'success_url' => route('payment.success', ['session_id' => '{CHECKOUT_SESSION_ID}']),
                'cancel_url' => route('payment.cancel'),
            ]);


            \Illuminate\Support\Facades\Session::put('CHECKOUT_SESSION_ID' ,$session->id);

            return redirect($session->url);
        } catch (\Exception $e) {
            Log::error('Error creating Checkout Session: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred while creating the checkout session.'], 500);
        }
    }



    public function success(Request $request)
    {

        $sessionId =  \Illuminate\Support\Facades\Session::get('CHECKOUT_SESSION_ID');
        if (!$sessionId) {
            return response()->json(['success' => false, 'message' => 'Session ID not found.'], 400);
        }
        Stripe::setApiKey(env('STRIPE_SECRET'));
        try {

            $session = \Stripe\Checkout\Session::retrieve($sessionId);
            if (!$session || $session->mode !== 'subscription') {
                return response()->json(['success' => false, 'message' => 'Invalid session.'], 404);
            }

            $subscriptionId = $session->subscription;
            $subscription = \Stripe\Subscription::retrieve($subscriptionId);

            $localSubscription = new \App\Models\Subscription();
            $localSubscription->user_id = auth()->user()->id;
            $localSubscription->stripe_subscription_id = $subscriptionId;
            $localSubscription->payment_status = '1';
            $localSubscription->amount = $subscription->plan->amount / 100;
            $localSubscription->start_date = date('Y-m-d H:i:s', $subscription->current_period_start);
            $localSubscription->end_date = date('Y-m-d H:i:s', $subscription->current_period_end);
            $localSubscription->save();

            // Save payment history
            $paymentHistory = new \App\Models\PaymentHistory();
            $paymentHistory->user_id = auth()->user()->id;
            $paymentHistory->stripe_subscription_id = $subscriptionId;
            $paymentHistory->payment_status = '1';
            $paymentHistory->amount = $subscription->plan->amount / 100;
            $paymentHistory->start_date = date('Y-m-d H:i:s', $subscription->current_period_start);
            $paymentHistory->end_date = date('Y-m-d H:i:s', $subscription->current_period_end);
            $paymentHistory->save();
            \Illuminate\Support\Facades\Session::forget('CHECKOUT_SESSION_ID');
            \Illuminate\Support\Facades\Session::put('status' ,'success');
            return redirect('student/dashboard');
        } catch (\Stripe\Exception\InvalidRequestException $exception) {
            \Illuminate\Support\Facades\Session::forget('CHECKOUT_SESSION_ID');
            Log::error('Error processing subscription: ' . $exception->getMessage());
            \Illuminate\Support\Facades\Session::put('status' ,'failed');
            return redirect('student/dashboard');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Session::forget('CHECKOUT_SESSION_ID');
            Log::error('Error processing subscription: ' . $e->getMessage());
            \Illuminate\Support\Facades\Session::put('status' ,'failed');
            return redirect('student/dashboard');
        }
    }


    public function cancel()
    {
        \Illuminate\Support\Facades\Session::forget('CHECKOUT_SESSION_ID');
        \Illuminate\Support\Facades\Session::put('status' ,'failed');
        return redirect('student/dashboard');
    }

    public function cancelSubscription()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        try {
            $subscriptions = \App\Models\Subscription::where('user_id', Auth::user()->id)->whereDate('end_date', '>', now())->first();
            $subscription = Subscription::retrieve($subscriptions->stripe_subscription_id);
            $subscription->cancel();
            $subscriptions->status = 'cancel';
            $subscriptions->save();
            \Illuminate\Support\Facades\Session::put('status' ,'cancel');
            return redirect('student/dashboard');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Session::put('status' ,'cancel');
            return redirect('student/dashboard');
        }
    }

}
