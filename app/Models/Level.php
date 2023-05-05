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
    protected $casts = [
        'age' => 'int',

    ];
    public function classes()
    {
        return $this->hasMany(Classe::class, 'level_id', 'id');
    }

    public function subjects(){
        return $this->hasMany(Subject::class,'level_id','id');
    }
}
