<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;


class Student extends Model
{
    use HasFactory;
    public $table = "student";
    protected $fillable = [
        'fullName', 'gender', 'motherName', 'motherLastName', 'birthday', 'phone', 'location', 'siblingNo', 'healthInfo', 'bus_id',
        'lng', 'lat', 'bus_registry', 'bus_discount', 'study_discount'
    ];
    public function owner(): MorphOne
    {
        return $this->morphOne(User::class, 'ownerable');
    }
    public function bus()
    {
        return $this->belongsTo(Bus::class, 'bus_id', 'id');
    }

    public function fees()
    {
        return $this->hasMany(StudentFees::class, 'student_id', 'id');
    }

    public function currentPayment()
    {
        return $this->hasMany(StudentFees::class, 'student_id', 'id')->latest('created_at')->first();
    }
    public function absences()
    {
        return $this->hasMany(Absence::class, "student_id", "id");
    }
    // public function class()
    // {
    //     return $this->belongsTo(Classe::class, "class_id", "id");
    // }

    public function class()
    {
        return $this->hasOne(StudentClass::class, 'student_id', 'id');
    }

    // public function classs(){
    //     return $this->hasOne(StudentClass::class,'student_id','id');
    // }

    public function buss()
    {
        return $this->hasOne(StudentBus::class, 'student_id', 'id');
    }

    public function notifications()
    {
        $this->belongsTo(Notification::class, 'student_id', 'id');
    }
}
