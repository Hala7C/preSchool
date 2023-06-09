<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;
    public $table="schools";
    protected $fillable = [
        'name', 'phone','lng','lat','start_time','bus_departure_time'
    ];
}
