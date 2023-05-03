<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    use HasFactory;
    protected $fillable = ["date", "student_id", "justification"];
    public function student()
    {
        return $this->belongsTo(Student::class, "student_id", "id");
    }
    public function scopeFilters(Builder $builder, $query)
    {
        $filters = array_merge([
            "date"    => Carbon::today(),
            "class_id" => null,
            "level_id" => null,
        ], $query);
        $builder->when($filters['date'], function ($builder, $value) {
            $builder->where('date', $value);
        });
        $builder->when($filters['class_id'], function ($builder, $value) {
            $builder->whereHas('student', function ($builder) use ($value) {
                $builder->where('class_id', $value);
            });
        });
        $builder->when($filters['level_id'], function ($builder, $value) {
            $builder->whereHas('student', function ($builder) use ($value) {
                $builder->whereHas('class', function ($builder) use ($value) {
                    $builder->where('level_id', '=', $value);
                });
            });
        });
    }
}
