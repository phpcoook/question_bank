<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $table = 'quiz';

    protected $fillable = [
        'answer',
        'time',
        'user_id',
        'question_id',
        'quiz_id'
    ];

    public function user()
    {
        return $this->hasMany(User::class);
    }

    public function question()
    {
        return $this->hasMany(Question::class);
    }

    public function questions()
    {
        return $this->belongsTo(Question::class);
    }
}
