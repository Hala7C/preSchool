<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;
class Subject extends Model
{
    use HasFactory;
    public $table = "subject";
    protected $fillable = [
        'name','level_id'
    ];

    // public function teachers(){
    //     // return $this->belongsToMany(Employee::class,'teacher_class_subject','subject_id','teacher_id',)
    //     return $this->belongsToMany(Employee::class,'teacher_class_subject');
    // }


    public function lessons(){
        return $this->hasMany(Lesson::class,'subject_id','id');
    }

    public function level(){
        return $this->belongsTo(Level::class,'level_id','id');
    }
    public function teachers(){
        return DB::table('employee')
        ->join('teacher_class_subject','employee.id','=','teacher_class_subject.teacher_id')
        ->where('teacher_class_subject.subject_id','=',$this->id)
        ->distinct()
        ->get(['employee.*']);    }
}
