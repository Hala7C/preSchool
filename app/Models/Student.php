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
        'fullName', 'gender', 'age', 'motherName', 'motherLastName', 'birthday', 'phone', 'location', 'siblingNo', 'healthInfo'
    ];



    public function owner(): MorphOne
    {
        return $this->morphOne(User::class, 'ownerable');
    }
}
