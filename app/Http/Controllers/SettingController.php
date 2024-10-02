<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Stripe\Stripe;
use Stripe\Product;
use Stripe\Price;

class SettingController extends Controller
{
    public function create()
    {
        $setting = Setting::first();
        return view('setting.create',compact('setting'));
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'no_of_question' => 'required|integer|min:1',
                'subscription_charge' => 'required|numeric|min:0',
            ]);
            if ($validator->fails()) {
                return back()->withInput()->withErrors($validator);
            } else {
                Stripe::setApiKey(env('STRIPE_SECRET'));

                $product = Product::create([
                    'name' => 'Paid',
                ]);

                $price = Price::create([
                    'product' => $product->id,
                    'unit_amount' => $request->input('subscription_charge') * 100, // Amount in cents
                    'currency' => 'usd',
                    'recurring' => [
                        'interval' => 'month',
                    ],
                ]);

                $setting = Setting::find($id);
                $setting->no_of_questions = $request->input('no_of_question');
                $setting->subscription_charge = $request->input('subscription_charge');
                $setting->stripe_price_id = $price->id;
                $setting->save();

                return redirect()->route('create.setting')->with('success', 'Setting Update successfully.');
            }
        } catch (\Exception $e) {
            Log::info('In File : ' . $e->getFile() . ' - Line : ' . $e->getLine() . ' - Message : ' . $e->getMessage() . ' - At Time : ' . date('Y-m-d H:i:s'));
            return redirect()->back()->with('error', 'An error occurred. Please try again.');
        }
    }
}
