<?php

namespace App\Http\Controllers\API;

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
        $cuurentYear=Carbon::now()->year;
        $data=collect();
        foreach($students as $std){
            $account=$std->owner;
            $date=explode('-',$std->birthday);
            $age=$cuurentYear-$date[0];
            $data->push([
                'id'=>$std->id,
                'fullName' => $std->fullName,
                'gender' => $std->gender,
                'motherName' => $std->motherName,
                'motherLastName' =>$std->motherLastName,
                'birthday' => $std->birthday,
                'age'=>$age,
                'phone' =>$std->phone,
                'location' => $std->location,
                'siblingNo' => $std->siblingNo,
                'healthInfo' => $std->healthInfo,
                'bus_id'=>$std->bus_id,
                'account_info'=>$account
            ]);

        }
        return ['data' => $data, 'status' => '210'];
    }

    public function store(Request $request)
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
            'bus_id'=>['required']
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
            'birthday' =>$date,
            'phone' => $request->phone,
            'location' => $request->location,
            'siblingNo' => $request->siblingNo,
            'healthInfo' => $request->healthInfo,
            'bus_id'=>$request->bus_id
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
            $data = ['message' => $exp->getMessage(), 'status' => 'failed'];
            $status = 400;
            return ['data' => $data, 'status' => $status];
        }
        $cuurentYear=Carbon::now()->year;
        $date=explode('-',$std->birthday);
        $age=$cuurentYear-$date[0];
        $data = collect();
        $data=([
            'message' => 'added successfully',
            'id'=>$std->id,
            'fullName' => $std->fullName,
            'gender' => $std->gender,
            'motherName' => $std->motherName,
            'motherLastName' =>$std->motherLastName,
            'birthday' => $std->birthday,
            'age' =>$age,
            'phone' =>$std->phone,
            'location' => $std->location,
            'siblingNo' => $std->siblingNo,
            'healthInfo' => $std->healthInfo,
            'bus_id'=>$std->bus_id,
            'account_info'=>$account,
            'pass' => $pass
        ]);

        return ['data' => $data, 'status' => 210];
    }


    public function show($id)
    {
        $std = Student::findOrFail($id);
        $cuurentYear=Carbon::now()->year;
        $date=explode('-',$std->birthday);
        $age=$cuurentYear-$date[0];
        $data=collect();
        $account=$std->owner;
        $data=([
            'id'=>$std->id,
            'fullName' => $std->fullName,
            'gender' => $std->gender,
            'motherName' => $std->motherName,
            'motherLastName' =>$std->motherLastName,
            'birthday' => $std->birthday,
            'age' =>$age,
            'phone' =>$std->phone,
            'location' => $std->location,
            'siblingNo' => $std->siblingNo,
            'healthInfo' => $std->healthInfo,
            'bus_id'=>$std->bus_id,
            'account_info'=>$account
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
            'bus_id'=>['required']

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
        $student->bus_id=$request->bus_id;
        $student->save();
        $res = collect();
        $cuurentYear=Carbon::now()->year;
        $date=explode('-',$student->birthday);
        $age=$cuurentYear-$date[0];
        $data=collect();
        $account=$student->owner;
        $res=([
            'message' => 'updated successfully',
            'id'=>$student->id,
            'fullName' => $student->fullName,
            'gender' => $student->gender,
            'motherName' => $student->motherName,
            'motherLastName' =>$student->motherLastName,
            'birthday' => $student->birthday,
            'age' =>$age,
            'phone' =>$student->phone,
            'location' => $student->location,
            'siblingNo' => $student->siblingNo,
            'healthInfo' => $student->healthInfo,
            'bus_id'=>$student->bus_id,
            'account_info'=>$account
        ]);
        return ['data' => $res, 'status' => 210];
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $account = $student->owner;
        $account->status = 'suspended';
        $account->save();
    }
}











      // $data = collect();
        // $data->push([
        //     'id'=>$student->id,
        //     'fullName'=>$student->fullName,
        //     'gender'=>$student->gender,
        //     'age'=>$student->age,
        //     'motherName'=>$student->motherName,
        //     'motherLastName'=>$student->motherLastName,
        //     'birthday'=>$student->birthday,
        //     'phone'=>$student->phone,
        //     'location'=>$student->location,
        //     'siblingNo'=>$student->siblingNo,
        //     'healthInfo'=>$student->healthInfo,
        //     'sequenceNo'=>$student->sequenceNo
        // ]);
