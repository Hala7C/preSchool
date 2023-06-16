<?php

namespace App\Http\Middleware;

use App\Models\Student;
use Closure;
use Illuminate\Http\Request;

class StudentDistributed
{
    public function handle(Request $request, Closure $next)
    {
        $students=Student::where('bus_registry',true)->count();
        $studentsLocations=Student::all()->whereNotNull('lat')->count();
        if ($students==$studentsLocations) {
                return $next($request);
        } else {
            return ['data'=>'Registered students have not updated their locations yet','status'=>400];
        }
    }
}
