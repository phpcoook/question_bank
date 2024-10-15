<?php

namespace App\Http\Controllers;

use App\Models\PaymentHistory;
use App\Models\Setting;
use App\Models\User;
use App\Models\WebHookLog;
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
    private $endpointSecret;

    public function __construct()
    {
        $this->paymentId = Setting::find(1);
        if (empty($this->paymentId)) {
            return redirect()->back()->with('error', 'Monthly Plan not Found');
        }
        $this->endpointSecret = env('STRIPE_WEBHOOK_SECRET');
    }

    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                if (Auth::user()->role == 'admin') {
                    $paymentHistory = PaymentHistory::with('user');
                } else {
                    $paymentHistory = PaymentHistory::select('users.email as email', 'payment_history.*')->join('users',
                        'users.id', 'payment_history.user_id')->where('users.id',
                        Auth::user()->id)->orderBy('payment_history.created_at', 'desc');
                }
                if (!empty($request->filter)) {
                    if ($request->filter == 'active') {
                        $paymentHistory->whereDate('end_date', '>', now());
                    } else {
                        $paymentHistory->whereDate('end_date', '<', now());
                    }
                }
                if (!empty($request->user)) {
                    $paymentHistory->where('user_id', $request->user);
                }

                $data = $paymentHistory->orderBy('created_at', 'desc')->get();
                $data->each(function ($item, $index) {
                    $item->index = $index;
                });

                if (Auth::user()->role == 'admin') {
                    return Datatables::of($data)
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
                            return '$' . $row->amount;
                        })
                        ->addColumn('status', function ($row) {
                            $currentDate = now();
                            return $row->end_date < $currentDate ? '<span class="text-danger">Expired</span>' : '<span class="text-success">Active</span>';
                        })
                        ->addColumn('stripe_id', function ($row) {
                            return $row->stripe_subscription_id;
                        })
                        ->rawColumns([
                            'no',
                            'subscriber',
                            'plan_start_date',
                            'plan_end_date',
                            'amount',
                            'status',
                            'stripe_id'
                        ])
                        ->make(true);
                } else {
                    return Datatables::of($data)
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
                            return '$' . $row->amount;
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
                $users = \App\Models\Subscription::with('user')->get();
                return view('payment.index', compact('users'));
            }
        } catch (\Exception $e) {
            Log::info('In File : ' . $e->getFile() . ' - Line : ' . $e->getLine() . ' - Message : ' . $e->getMessage() . ' - At Time : ' . date('Y-m-d H:i:s'));
            return redirect()->back()->with('error', 'An error occurred. Please try again.');
        }
    }


    //New redirect Method

    public function createCheckoutSession(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price' => $this->paymentId->stripe_price_id,
                        'quantity' => 1,
                    ]
                ],
                'mode' => 'subscription',
                'success_url' => route('payment.success', ['session_id' => '{CHECKOUT_SESSION_ID}']),
                'cancel_url' => route('payment.cancel'),
                'customer_email' => Auth::user()->email,
            ]);


            \Illuminate\Support\Facades\Session::put('CHECKOUT_SESSION_ID', $session->id);

            return redirect($session->url);
        } catch (\Exception $e) {
            Log::error('Error creating Checkout Session: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the checkout session.'
            ], 500);
        }
    }

    public function success(Request $request)
    {

        $sessionId = \Illuminate\Support\Facades\Session::get('CHECKOUT_SESSION_ID');
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
            $this->savePayment(auth()->user()->id, $subscriptionId, $subscription->plan->amount,
                $subscription->current_period_start, $subscription->current_period_end,'save');
            \Illuminate\Support\Facades\Session::forget('CHECKOUT_SESSION_ID');
            \Illuminate\Support\Facades\Session::put('status', 'success');
            return redirect('student/dashboard');
        } catch (\Stripe\Exception\InvalidRequestException $exception) {
            \Illuminate\Support\Facades\Session::forget('CHECKOUT_SESSION_ID');
            Log::error('Error processing subscription: ' . $exception->getMessage());
            \Illuminate\Support\Facades\Session::put('status', 'failed');
            return redirect('student/dashboard');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Session::forget('CHECKOUT_SESSION_ID');
            Log::error('Error processing subscription: ' . $e->getMessage());
            \Illuminate\Support\Facades\Session::put('status', 'failed');
            return redirect('student/dashboard');
        }
    }


    public function cancel()
    {
        \Illuminate\Support\Facades\Session::forget('CHECKOUT_SESSION_ID');
        \Illuminate\Support\Facades\Session::put('status', 'failed');
        return redirect('student/dashboard');
    }

    public function cancelSubscription()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        try {
            $subscriptions = \App\Models\Subscription::where('user_id', Auth::user()->id)->whereDate('end_date', '>',
                now())->first();
            $subscription = Subscription::retrieve($subscriptions->stripe_subscription_id);
            $subscription->cancel();
            $subscriptions->status = 'cancel';
            $subscriptions->save();
            \Illuminate\Support\Facades\Session::put('status', 'cancel');
            return redirect('student/dashboard');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Session::put('status', 'cancel');
            return redirect('student/dashboard');
        }
    }

    public function handleWebHook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->headers->get('Stripe-Signature');
        $webHookData = new WebHookLog();
        $webHookData->payload = json_encode([$payload], JSON_PRETTY_PRINT);
        $webHookData->sign_header = json_encode([$sigHeader], JSON_PRETTY_PRINT);
        $webHookData->save();

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $this->endpointSecret);
        } catch (\UnexpectedValueException $e) {
            Log::info('error : Invalid payload' . $e->getTraceAsString());
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::info('error : Invalid signature' . $e->getTraceAsString());
            return response()->json(['error' => 'Invalid signature'], 400);
        }
        switch ($event['type']) {
            case 'invoice.paid':
            case 'customer.subscription.updated':
                $subscription = $event['data']['object'];
                $startDate = date('Y-m-d', $subscription['current_period_start']);
                $endDate = date('Y-m-d', $subscription['current_period_end']);
                $stripeSubscriptionId = $subscription['id'];
                $customerId = $subscription['customer'];
                $customer = \Stripe\Customer::retrieve($customerId);
                $email = $customer->email;

                $amount = isset($subscription['latest_invoice'])
                    ? $subscription['latest_invoice']['amount_paid']
                    : null;
                $user_id = User::where('email', $email)->first();
                if(!empty($user_id)) {
                    $this->savePayment($user_id->id, $stripeSubscriptionId, $amount, $startDate, $endDate,'update');
                }
                Log::info('Subscription Details:', [
                    'stripe_subscription_id' => $stripeSubscriptionId,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'amount' => $amount / 100,
                    'email' => $email,
                    'update' => 'success'
                ]);
                break;

            default:
                break;
        }

        return response()->json(['status' => 'success']);
    }

    private function savePayment($user_id, $subscriptionId, $amount, $current_period_start, $current_period_end,$type)
    {
        if($type == 'update') {
            $localSubscription = \App\Models\Subscription::where('user_id', $user_id)->first();
        }else{
            $localSubscription = new \App\Models\Subscription();
        }
        $localSubscription->user_id = $user_id;
        $localSubscription->stripe_subscription_id = $subscriptionId;
        $localSubscription->payment_status = '1';
        $localSubscription->amount = $amount / 100;
        $localSubscription->start_date = date('Y-m-d H:i:s', $current_period_start);
        $localSubscription->end_date = date('Y-m-d H:i:s', $current_period_end);
        $localSubscription->save();

        // Save payment history
        $paymentHistory = new \App\Models\PaymentHistory();
        $localSubscription->user_id = $user_id;
        $localSubscription->stripe_subscription_id = $subscriptionId;
        $localSubscription->payment_status = '1';
        $localSubscription->amount = $amount / 100;
        $localSubscription->start_date = date('Y-m-d H:i:s', $current_period_start);
        $localSubscription->end_date = date('Y-m-d H:i:s', $current_period_end);
        $paymentHistory->save();
    }


}
