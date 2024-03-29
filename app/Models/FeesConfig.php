<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeesConfig extends Model
{
    use HasFactory;
    public $table = "fees_config";
    protected $fillable = [
        'date', 'amount'
    ];

    public function notifications()
    {
        return $this->belongsTo(Notification::class, 'config_id', 'id');
    }
}
