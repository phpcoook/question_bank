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

}
