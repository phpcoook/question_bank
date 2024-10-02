<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $table = 'subscriptions';


    public function user()
    {
        return $this->hasOne(User::class,'id','user_id')->where('role', 'student');
    }

}
