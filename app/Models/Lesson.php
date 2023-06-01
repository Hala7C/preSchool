<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    public  $table = 'lessons';
    protected $fillable = [
        'title',    'semester',    'number', 'subject_id'
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'id');
    }

    public function homeworks()
    {
        return $this->hasMany(Homework::class, 'lesson_id', 'id');
    }

    public function classes(){
        return $this->belongsToMany(Classe::class,'lesson_class','class_id','lesson_id');
    }
}
