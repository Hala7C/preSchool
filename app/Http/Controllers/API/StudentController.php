<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;

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
        return ['data' => $students, 'status' => '210'];
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
            'healthInfo' => ['string', 'alpha', 'max:255']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $input = [
            'fullName' => $request->fullName,
            'gender' => $request->gender,
            'motherName' => $request->motherName,
            'motherLastName' => $request->motherLastName,
            'birthday' => $request->birthday,
            'phone' => $request->phone,
            'location' => $request->location,
            'siblingNo' => $request->siblingNo,
            'healthInfo' => $request->healthInfo
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

        $data = collect();
        $data->push([
            'student info' => $std,
            'account info' => $account,
            'pass' => $pass
        ]);
        $res = collect();
        $res->push([
            'message' => 'added successfully',
            'data' => $data
        ]);
        return ['data' => $res, 'status' => 210];
    }


    public function show($id)
    {
        $student = Student::findOrFail($id);
        return ['data' => $student, 'status' => '210'];
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
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $student->fullName = $request->fullName;
        $student->gender = $request->gender;
        $student->motherName = $request->motherName;
        $student->motherLastName = $request->motherLastName;
        $student->birthday = $request->birthday;
        $student->phone = $request->phone;
        $student->location = $request->location;
        $student->siblingNo = $request->siblingNo;
        $student->healthInfo = $request->healthInfo;
        $student->save();
        $res = collect();
        $res->push([
            'message' => 'updated successfully',
            'data' => $student
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
