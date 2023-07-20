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
            "bus_id" => null
        ], $query);
        $builder->when($filters['date'], function ($builder, $value) {
            $builder->where('date', $value);
        });
        $builder->when($filters['class_id'], function ($builder, $value) {
            $builder->whereHas('student', function ($builder) use ($value) {
                $builder->whereHas('classs', function ($builder) use ($value) {
                    $builder->where('class_id', '=', $value);
                });
            });
        });
        $builder->when($filters['bus_id'], function ($builder, $value) {
            $builder->whereHas('student', function ($builder) use ($value) {
                $builder->whereHas('buss', function ($builder) use ($value) {
                    $builder->where('bus_id', '=', $value);
                });
            });
        });
        $builder->when($filters['level_id'], function ($builder, $value) {
            $builder->join('student', 'absences.student_id', '=', 'student.id')
                ->join('student_class', 'student_class.student_id', '=', 'student.id')
                ->join('class', 'student_class.class_id', '=', 'class.id')
                ->where('level_id', $value);
        });
    }
}
