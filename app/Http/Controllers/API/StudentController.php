<?php

namespace App\Http\Controllers\API;

use App\Events\EmployeeNotifi;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Database\Events\TransactionBeginning;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{

    public function index()
    {
        $students = Student::all();
        if ($students->isEmpty()) {
            return ['data' => 'there is no student', 'status' => '210'];
        }
        $cuurentYear = Carbon::now()->year;
        $data = collect();
        foreach ($students as $std) {
            $account = $std->owner;
            $date = explode('-', $std->birthday);
            $age = $cuurentYear - $date[0];
            $data->push([
                'id' => $std->id,
                'fullName' => $std->fullName,
                'gender' => $std->gender,
                'motherName' => $std->motherName,
                'motherLastName' => $std->motherLastName,
                'birthday' => $std->birthday,
                'age' => $age,
                'phone' => $std->phone,
                'location' => $std->location,
                'siblingNo' => $std->siblingNo,
                'healthInfo' => $std->healthInfo,
                'bus_registry' => $std->bus_registry,
                'bus_id' => $std->bus_id,
                'lng' => $std->lng,
                'lat' => $std->lat,
                'bus_discount'=>$std->bus_discount,
                'study_discount'=>$std->study_discount,
                'class_id'=>$std->classs()->get(),
                'account_info' => $account
            ]);
        }
        return ['data' => $data, 'status' => '210'];
    }


    public function show($id)
    {
        $std = Student::findOrFail($id);
        $cuurentYear = Carbon::now()->year;
        $date = explode('-', $std->birthday);
        $age = $cuurentYear - $date[0];
        $data = collect();
        $account = $std->owner;
        $data = ([
            'id' => $std->id,
            'fullName' => $std->fullName,
            'gender' => $std->gender,
            'motherName' => $std->motherName,
            'motherLastName' => $std->motherLastName,
            'birthday' => $std->birthday,
            'age' => $age,
            'phone' => $std->phone,
            'location' => $std->location,
            'siblingNo' => $std->siblingNo,
            'healthInfo' => $std->healthInfo,
            'bus_registry' => $std->bus_registry,
            'bus_discount'=>$std->bus_discount,
            'study_discount'=>$std->study_discount,
            'bus_id' => $std->bus_id,
            'lng' => $std->lng,
            'lat' => $std->lat,
            'account_info' => $account
        ]);
        return ['data' => $data, 'status' => '210'];
    }


    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'fullName' => ['required', 'alpha', 'max:255'],
            'gender' => ['required', 'in:male,female'],
            'motherName' => ['required', 'alpha', 'max:255'],
            'motherLastName' => ['required', 'alpha', 'max:255'],
            'birthday' => ['required'],
            'phone' => ['required', 'digits:10'],
            'location' => ['required', 'string'],
            'siblingNo' => ['required', 'numeric'],
            'healthInfo' => ['string', 'alpha', 'max:255'],
            'bus_registry' => ['required', 'boolean'],
            'bus_discount'=>['required', 'boolean'],
            'study_discount'=>['required', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $myDate =  $request->birthday;
        $date = Carbon::createFromFormat('d/m/Y', $myDate)->format('Y-m-d');
        $student->fullName = $request->fullName;
        $student->gender = $request->gender;
        $student->motherName = $request->motherName;
        $student->motherLastName = $request->motherLastName;
        $student->birthday = $date;
        $student->phone = $request->phone;
        $student->location = $request->location;
        $student->siblingNo = $request->siblingNo;
        $student->healthInfo = $request->healthInfo;
        $student->bus_registry=$request->bus_registry;

        $student->bus_discount=$request->bus_discount;
        $student->study_discount=$request->study_discount;

        $student->save();
        $res = collect();
        $cuurentYear = Carbon::now()->year;
        $date = explode('-', $student->birthday);
        $age = $cuurentYear - $date[0];
        $data = collect();
        $account = $student->owner;
        $res = ([
            'message' => 'updated successfully',
            'id' => $student->id,
            'fullName' => $student->fullName,
            'gender' => $student->gender,
            'motherName' => $student->motherName,
            'motherLastName' => $student->motherLastName,
            'birthday' => $student->birthday,
            'age' => $age,
            'phone' => $student->phone,
            'location' => $student->location,
            'siblingNo' => $student->siblingNo,
            'healthInfo' => $student->healthInfo,
            'bus_registry' => $student->bus_registry,
            'bus_discount'=>$student->bus_discount,
            'study_discount'=>$student->study_discount,
            'account_info' => $account
        ]);
        return ['data' => $res, 'status' => 210];
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $account = $student->owner;
        $account->status = 'suspended';
        $account->save();
        return ['data'=>'suspended successfully','status'=>210];
    }

    public function updateStudentLocation(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'lng' => ['required', 'numeric'],
            'lat' => ['required', 'numeric'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $student->update([
            'lng' => $request->lng,
            'lat' => $request->lat,
        ]);
        ///* send notification to employee if current time greater than last
        // distributed
        event(new EmployeeNotifi());



        return ['data' => 'student location updated successfully'];
    }
    ///////////////////////////////////////////////////
    //////////copy
    public function store1(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fullName' => ['required', 'alpha', 'max:255'],
            'gender' => ['required', 'in:male,female'],
            'motherName' => ['required', 'alpha', 'max:255'],
            'motherLastName' => ['required', 'alpha', 'max:255'],
            'birthday' => ['required'],
            'phone' => ['required', 'digits:10'],
            'location' => ['required', 'string'],
            'siblingNo' => ['required', 'numeric'],
            'healthInfo' => ['string', 'alpha', 'max:255'],
            'bus_registry' => ['required', 'boolean'],
            'bus_discount'=>['required', 'boolean'],
            'study_discount'=>['required', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);

        }

        $myDate =  $request->birthday;
        $date = Carbon::createFromFormat('d/m/Y', $myDate)->format('Y-m-d');
        $input = [
            'fullName' => $request->fullName,
            'gender' => $request->gender,
            'motherName' => $request->motherName,
            'motherLastName' => $request->motherLastName,
            'birthday' => $date,
            'phone' => $request->phone,
            'location' => $request->location,
            'siblingNo' => $request->siblingNo,
            'healthInfo' => $request->healthInfo,
            'bus_registry' => $request->bus_registry,
            'bus_discount'=>$request->bus_discount,
            'study_discount'=>$request->study_discount,

        ];
        DB::beginTransaction();
        try {
            $std = Student::create($input);
            $pass = Str::random(7);
            $account = $std->owner()->create([
                'name' => Str::random(5),
                'role' => 'user',
                'password' => Hash::make($pass),
                'status' => 'active'
            ]);
            DB::commit();
        } catch (\Exception $exp) {

            DB::rollBack(); // Tell Laravel, "It's not you, it's me. Please don't persist to DB"
            return ['data' =>  $exp->getMessage(), 'status' => 400];
        }
        $cuurentYear = Carbon::now()->year;
        $date = explode('-', $std->birthday);
        $age = $cuurentYear - $date[0];
        $data = ([
            'message' => 'added successfully',
            'id' => $std->id,
            'fullName' => $std->fullName,
            'gender' => $std->gender,
            'motherName' => $std->motherName,
            'motherLastName' => $std->motherLastName,
            'birthday' => $std->birthday,
            'age' => $age,
            'phone' => $std->phone,
            'location' => $std->location,
            'siblingNo' => $std->siblingNo,
            'healthInfo' => $std->healthInfo,
            'bus_registry' => $std->bus_registry,
            'bus_discount'=>$std->bus_discount,
            'study_discount'=>$std->study_discount,
            'account_info' => $account,
            'pass' => $pass
        ]);

        return ['data' => $data, 'status' => 210];

    }
}
