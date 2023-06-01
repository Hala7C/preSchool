<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;
    public $table="answers";
    protected $fillable=['text','img','symbol','correct_answer','question_id'];

    public function question(){
        return $this->belongsTo(Question::class,'question_id','id');
    }
}
