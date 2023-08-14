<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Mockery\Matcher\Subset;
use Illuminate\Support\Facades\DB;

class Exam extends Model
{
    public $table = 'exams';
    protected $fillable = [
        'name', 'file_path', 'status', 'term', 'type', 'publish_date', 'subject_id', 'teacher_id'
    ];
    use HasFactory;

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'id');
    }

    public function teacher()
    {
        return $this->belongsTo(Employee::class, 'teacher_id', 'id');
    }

    public function class($subID, $teacher_id)
    {
        return DB::table('class')
            ->join('teacher_class_subject', 'class.id', '=', 'teacher_class_subject.class_id')
            ->where('teacher_class_subject.subject_id', '=', $subID)
            ->where('teacher_class_subject.teacher_id', '=', $teacher_id)
            ->distinct()
            ->get(['class.*']);
    }
    public function markets()
    {
        return $this->hasMany(Market::class, 'exam_id', 'id');
    }
}
