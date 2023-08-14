<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mark extends Model
{
    use HasFactory;
    public $table = "marks";
    protected $fillable = ['exam_id', 'student_id', 'mark'];
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }
    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id', 'id');
    }
}
