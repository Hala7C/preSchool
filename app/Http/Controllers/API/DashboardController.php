<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use App\Models\StudentFees;
use App\Models\YearConfig;

class DashboardController extends Controller
{
    //count of student
    public function getCountStudent()
    {
        $student_count = Student::all()->count();
        $employee_count = User::where('role', 'employee')->count();
        $teacher_count = User::where('role', 'teacher')->count();
        $bus_count = Bus::all()->count();
        $data = collect();
        $data->push([
            'student_count' => $student_count,
            'employee_count' => $employee_count,
            'teacher_count' => $teacher_count,
            'bus_count' => $bus_count
        ]);
        return ['data' => $data, 'status' => '210'];
    }
    public function getCountEmployee()
    {
        $employee_count = User::where('role', 'employee')->count();
        return ['data' => $employee_count, 'status' => '210'];
    }
    public function getCountTeacher()
    {
        $teacher_count = User::where('role', 'teacher')->count();
        return ['data' => $teacher_count, 'status' => '210'];
    }
    public function getCountBus()
    {
        $bus_count = Bus::all()->count();
        return ['data' => $bus_count, 'status' => '210'];
    }
    public function getFeesRate()
    {
        $cuurentYear = Carbon::now()->year;
        $data = (new StudentFeesController)->allStudentInfo();
        $paided = StudentFees::sum('amount');
        $fees = YearConfig::where('year', '=', $cuurentYear)->get();
        $full = ($fees[0]->study_fees + $fees[0]->bus_fees) * count($data['data']);
        $unpaided = $full - $paided;
        $data = collect();
        $data->push([
            'full' => $full,
            'paided' => $paided,
            'unpaided' => $unpaided,
        ]);
        return ['data' => $data, 'status' => '210'];
    }
    public function busRate()
    {
        $student_with_bus = Student::whereNotNull('bus_id')->count();
        $student_without_bus = Student::whereNull('bus_id')->count();
        $data = collect();
        $data->push([
            'student_with_bus' => $student_with_bus,
            'student_without_bus' => $student_without_bus,
        ]);
        return ['data' => $data, 'status' => '210'];
    }
}
