<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubTopic extends Model
{
    use HasFactory;

    protected $table = 'sub_topics';

    public function topics(){
        return $this->hasOne(Topic::class,'id','topic_id');
    }
}
