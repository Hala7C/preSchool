<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    use HasFactory;
    public $table="categories";
    protected $fillable=["name","img"];

    public function questions(){
        return $this->hasMany(Question::class,'category_id','id');
    }
}
