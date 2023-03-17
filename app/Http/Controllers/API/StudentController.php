<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{

    public function index()
    {
        $students = Student::all();
        // $data = collect();
        // foreach($students as $s){
        //     $data->push([
        //         'id'=>$s->id,
        //         'fullName'=>$s->fullName,
        //         'gender'=>$s->gender,
        //         'age'=>$s->age,
        //         'motherName'=>$s->motherName,
        //         'motherLastName'=>$s->motherLastName,
        //         'birthday'=>$s->birthday,
        //         'phone'=>$s->phone,
        //         'location'=>$s->location,
        //         'siblingNo'=>$s->siblingNo,
        //         'healthInfo'=>$s->healthInfo,
        //         'sequenceNo'=>$s->sequenceNo
        //     ]);
        // }
        return ['data' => $students, 'status' => '210'];
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'fullName' => ['required', 'string'],
            'gender' => ['required', 'string'],
            'age' => ['required', 'string'],
            'motherName' => ['required', 'string'],
            'motherLastName' => ['required', 'string'],
            'birthday' => ['required', 'string'],
            'phone' => ['required', 'string'],
            'location' => ['required', 'string'],
            'siblingNo' => ['required', 'string'],
            'healthInfo' => ['string'],
            'sequenceNo' => ['required', 'string']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }


        $input = [
            'fullName' => $request->fullName,
            'gender' => $request->gender,
            'age' => $request->age,
            'motherName' => $request->motherName,
            'motherLastName' => $request->motherLastName,
            'birthday' => $request->birthday,
            'phone' => $request->phone,
            'location' => $request->location,
            'siblingNo' => $request->siblingNo,
            'healthInfo' => $request->healthInfo,
            'sequenceNo' => $request->sequenceNo
        ];
        $std = Student::create($input);
        $pass = Str::random(7);
        $account = User::create([
            'name' => Str::random(5),
            'role' => 'user',
            'password' => Hash::make($pass),
            'status' => 'active',
            'ownerable_id' => $std->id,
        ]);
        $data = collect();
        $data->push([
            'student info' => $std,
            'account info' => $account,
            'pass' =>$pass
        ]);
        return ['data' => $data, 'status' => 210];
    }


    public function show($id)
    {
        $student = Student::findOrFail($id);
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
        return ['data' => $student, 'status' => '210'];
    }


    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        $student->fullName = $request->fullName;
        $student->gender = $request->gender;
        $student->age = $request->age;
        $student->motherName = $request->motherName;
        $student->motherLastName = $request->motherLastName;
        $student->birthday = $request->birthday;
        $student->phone = $request->phone;
        $student->location = $request->location;
        $student->siblingNo = $request->siblingNo;
        $student->healthInfo = $request->healthInfo;
        $student->sequenceNo = $request->sequenceNo;
        $student->save();
        return ['data' => $student, 'status' => 210];
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $account = $student->owner;
        /*
        panding account status [use event|listener]
         */
        $account->status='suspended';
        $account->save();
    }
}
