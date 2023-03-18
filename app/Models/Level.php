<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    use HasFactory;
    public $table = "level";
    protected $fillable = [
        'name', 'age'
    ];
    public function classes()
    {
        return $this->hasMany(Classe::class, 'level_id', 'id');
    }
}
