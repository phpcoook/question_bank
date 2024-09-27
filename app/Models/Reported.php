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
        return $this->hasMany(User::class);
    }

    public function question()
    {
        return $this->hasMany(Question::class);
    }
}
