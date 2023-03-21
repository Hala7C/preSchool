<?php

namespace App\Models;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bus extends Model
{
    use HasFactory;
    public $table = "bus";
    protected $fillable = [
        'capacity', 'number','bus_supervisor_id'
    ];

    public function supervisor(){
        return $this->belongsTo(Employee::class,'bus_supervisor_id','id');
    }

    public function students(){
        return $this->hasMany(Student::class,'bus_id','id');
    }
}
