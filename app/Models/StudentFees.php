<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;


class StudentFees extends Model
{
    use HasFactory;
    public $table = "student_fees";
    protected $fillable = [
        'amount',	'remaind'	,'student_id'
    ];

    public function student(){
        return $this->belongsTo(Student::class,'student_id','id');
    }
}
