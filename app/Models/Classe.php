<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classe extends Model
{
    use HasFactory;
    public $table = "class";
    protected $fillable = [
        'name', 'capacity', 'level_id'
    ];
    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id', 'id');
    }
}
