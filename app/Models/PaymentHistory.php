<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentHistory extends Model
{
    use HasFactory;

    protected $table = 'payment_history';

    protected $fillable = [
        'user_id',
        'stripe_subscription_id',
        'payment_status',
        'amount',
        'start_date',
        'end_date',
    ];

    public function user()
    {
        return $this->hasOne(User::class,'id','user_id')->where('role', 'student');
    }
    
}
