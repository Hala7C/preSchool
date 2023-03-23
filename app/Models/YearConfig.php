<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YearConfig extends Model
{
    use HasFactory;
    public $table = "year_config";
    protected $fillable = [
        'year', 'study_fees', 'bus_fees', 'discount_bus', 'discount_without_bus'
    ];
}
