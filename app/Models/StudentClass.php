<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentClass extends Model
{
    use HasFactory;
    public $table = "student_class";
    protected $fillable = [
        'class_id', 'student_id'
    ];

    public function student(){
        return $this->belongsTo(Student::class,'student_id','id');
    }

    public function class(){
        return $this->belongsToMany(Classe::class,'class_id','id');
    }
}
