<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Classe extends Model
{
    use HasFactory;
    public $table = "class";
    protected $fillable = [
        'name', 'capacity', 'level_id'
    ];
    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id', 'id');
    }


    public function lessons(){
        return $this->belongsToMany(Lesson::class,'lesson_class','class_id','lesson_id');
    }

    // public function teachers(){
    //     return $this->belongsToMany(Employee::class,'teacher_class_subject');
    // }

    public function teachers(){
        return DB::table('employee')
                    ->join('teacher_class_subject','employee.id','=','teacher_class_subject.teacher_id')
                    ->where('teacher_class_subject.class_id','=',$this->id)
                    ->distinct()
                    ->get('employee.*');
    }

    public function students(){
        return $this->hasMany(StudentClass::class,'class_id','id');
    }

}
