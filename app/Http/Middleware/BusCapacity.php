<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Bus;
use App\Models\Student;

class BusCapacity
{

    public function handle(Request $request, Closure $next)
    {
        $busCapacity = Bus::all()->sum('capacity');
        $studentNumber = Student::all()->where('bus_registry', true)->count();
        $remindStudent = $studentNumber - $busCapacity;
        if ($busCapacity >= $studentNumber) {
            return $next($request);;
        } else {
            return ['data'=>'the buses capacity is not enough !! there is ' . $remindStudent . ' students without seat !!','status'=>400];
        }
    }
}
