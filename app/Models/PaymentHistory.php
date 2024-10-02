<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentHistory extends Model
{
    use HasFactory;

    protected $table = 'payment_history';


    public function user()
    {
        return $this->hasOne(User::class,'id','user_id')->where('role', 'student');
    }
    
}
