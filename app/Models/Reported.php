<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reported extends Model
{
    use HasFactory;
    protected $table = 'reported';

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
