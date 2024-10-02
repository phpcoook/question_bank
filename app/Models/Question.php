<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $table = 'question';


    public function images()
    {
        return $this->hasMany(QuestionImage::class);
    }

    public function quizImage()
    {
        return $this->hasMany(QuestionImage::class)->where('type', 'question');
    }

    public function ansImage()
    {
        return $this->hasMany(QuestionImage::class)->where('type', 'answer');
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

}
