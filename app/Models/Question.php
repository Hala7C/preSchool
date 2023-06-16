<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;
    public $table="questions";
    protected $fillable=["text","audio","category_id"];

    public function category(){
        return $this->belongsTo(Category::class,"category_id",'id');
    }

    public function answers(){
        return $this->hasMany(Answer::class,"question_id","id");
    }
}