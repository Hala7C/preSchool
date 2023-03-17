<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

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
}
