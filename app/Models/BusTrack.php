<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusTrack extends Model
{
    use HasFactory;
    protected $fillable = [
        'bus_id', 'lng', 'lat',
    ];
    public function bus()
    {
        return $this->belongsTo(Bus::class, 'bus_id', 'id');
    }
}
