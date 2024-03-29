<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    public $table = "notifications";
    protected $fillable = [
        'student_id',    'current_remaining_payment', 'type', 'config_id'
    ];
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }

    public function config()
    {
        return $this->belongsTo(FeesConfig::class, 'config_id', 'id');
    }
}
