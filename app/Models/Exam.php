<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Mockery\Matcher\Subset;
use Illuminate\Support\Facades\DB;
class Exam extends Model
{
    public $table='exams';
    protected $fillable=[
        'name','file_path','status','term','type','publish_date','subject_id','teacher_id'
    ];
    use HasFactory;

    public function subject(){
        return $this->belongsTo(Subject::class,'subject_id','id');
    }

    public function teacher(){
        return $this->belongsTo(Employee::class,'teacher_id','id');
    }

    public function class(){
        return DB::table('class')
        ->join('teacher_class_subject','class.id','=','teacher_class_subject.class_id')
        ->where('teacher_class_subject.subject_id','=',$this->subject()->get('id'))
        ->where('teacher_class_subject.teacher_id','=',$this->teacher()->get('id'))
        ->distinct()
        ->get(['class.*']);
    }
}
