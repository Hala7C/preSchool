<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentBus extends Model
{
    use HasFactory;
    public $table = "student_bus";
    protected $fillable = [
        'lng','lat','arrival_time','bus_id', 'student_id'
    ];

    public function student(){
        return $this->belongsTo(Student::class,'student_id','id');
    }

    public function bus(){
        return $this->belongsToMany(Bus::class,'bus_id','id');
    }
}
