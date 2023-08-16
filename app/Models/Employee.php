<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\DB;

class Employee extends Model
{
    use HasFactory;
    public $table = "employee";

    protected $fillable = [
        'fullName',
        'gender',
        'birthday',
        'phone',
        'location',
        'healthInfo',
        'degree',
        'specialization'
    ];
    public function owner(): MorphOne
    {
        return $this->morphOne(User::class, 'ownerable');
    }

    public function bus()
    {
        return $this->hasOne(Bus::class, 'bus_supervisor_id', 'id');
    }

    // public function classes(){
    //     return $this->belongsToMany(Classe::class,'teacher_class_subject');
    // }

    // public function subjects(){
    //     return $this->belongsToMany(Subject::class,'teacher_class_subject');
    // }

    public function classes()
    {
        return DB::table('class')
            ->join('teacher_class_subject', 'class.id', '=', 'teacher_class_subject.class_id')
            ->where('teacher_class_subject.teacher_id', '=', $this->id)
            ->distinct()
            ->get(['class.*']);
    }

    public function classess($id)
    {
        return DB::table('class')
            ->join('teacher_class_subject', 'class.id', '=', 'teacher_class_subject.class_id')
            ->where('teacher_class_subject.teacher_id', '=', $id)
            ->distinct()
            ->get(['class.*']);
    }

    public function subjects($id)
    {
        return DB::table('subject')
            ->join('teacher_class_subject', 'subject.id', '=', 'teacher_class_subject.subject_id')
            ->where('teacher_class_subject.teacher_id', '=', $id)
            ->distinct()
            ->get(['subject.*']);
    }

    public function fees()
    {
        return $this->hasMany(StudentFees::class, 'employee_id', 'id');
    }
}
