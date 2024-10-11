<?php

namespace App\Services;

use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class CustomService
{

    public static function checkSubscription()
    {
        try {
            $user = Auth::user();
            $subscription = Subscription::where('user_id', $user->id)
                ->whereDate('end_date', '>', now())
                ->first();
            if ($subscription && $user->subscription_status) {
                return true;
            }
            return false;
        } catch (\Exception $e) {
            Log::error("An error occurred: " . $e->getMessage());
            return false;
        }
    }

}