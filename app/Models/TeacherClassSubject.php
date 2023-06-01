<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherClassSubject extends Model
{
    use HasFactory;
    public $table = 'teacher_class_subject';
    protected $fillable = [
        'class_id',
        'teacher_id',
        'subject_id'
    ];


}
