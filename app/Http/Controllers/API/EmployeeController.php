<?php

namespace App\Http\Controllers\API;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index()
    {
        $Employees = Employee::all();
        if ($Employees->isEmpty()) {
            return ['data' => 'there is no student', 'status' => '210'];
        }
        $data=collect();

        $cuurentYear=Carbon::now()->year;

        foreach($Employees as $emp){
            $account=$emp->owner;
            $date=explode('-',$emp->birthday);
            $age=$cuurentYear-$date[0];
            $data->push([
                'fullName' => $emp->fullName,
                'gender' => $emp->gender,
                'birthday' =>$emp->birthday,
                'age'=>$age,
                'phone' => $emp->phone,
                'location' => $emp->location,
                'healthInfo' => $emp->healthInfo,
                'degree' => $emp->degree,
                'specialization'=>$emp->specialization,
                'account_info'=>$account
            ]);
        }

        return ['data' => $data, 'status' => '210'];
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'fullName' => ['required', 'alpha', 'max:255'],
            'gender' => ['required','in:male,female'],
            'birthday' => ['required'],
            'phone' => ['required', 'digits:10'],
            'location' => ['required', 'string'],
            'healthInfo' => ['sometimes','string','alpha', 'max:255'],
            'degree' => ['required','in:bachalor,bachalors,master'],
            'specialization'=>['sometimes'],
            'role'=>['required','in:teacher,manager,employee,bus_supervisor,admin']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $myDate =  $request->birthday;
        $date = Carbon::createFromFormat('m/d/Y', $myDate)->format('Y-m-d');
        $input = [
            'fullName' => $request->fullName,
            'gender' => $request->gender,
            'birthday' =>$date,
            'phone' => $request->phone,
            'location' => $request->location,
            'healthInfo' => $request->healthInfo,
            'degree' => $request->degree,
            'specialization'=>$request->specialization
        ];
        DB::beginTransaction();
        try {
            $emp = Employee::create($input);
            $pass = Str::random(7);
            $account =$emp->owner()->create([
                'name' => Str::random(5),
                'role' => $request->role,
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
            'Employee info' => $emp,
            'account info' => $account,
            'pass' =>$pass
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
        $emp = Employee::findOrFail($id);
        $account=$emp->owner;
        $cuurentYear=Carbon::now()->year;
        $date=explode('-',$emp->birthday);
        $age=$cuurentYear-$date[0];
        $data=collect();
        $data->push([
            'fullName' => $emp->fullName,
            'gender' => $emp->gender,
            'birthday' =>$emp->birthday,
            'age'=>$age,
            'phone' => $emp->phone,
            'location' => $emp->location,
            'healthInfo' => $emp->healthInfo,
            'degree' => $emp->degree,
            'specialization'=>$emp->specialization,
            'account_info'=>$account
        ]);
        return ['data' => $data, 'status' => '210'];
    }


    public function update(Request $request, $id)
    {
        $Employee = Employee::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'fullName' => ['required', 'alpha', 'max:255'],
            'gender' => ['required','in:male,female'],
            'birthday' => ['required'],
            'phone' => ['required', 'digits:10'],
            'location' => ['required', 'string'],
            'healthInfo' => ['string','alpha', 'max:255'],
            'degree' => ['required','in:bachalor,bachalors,master'],
            'specialization'=>['required'],
            'role'=>['required','in:teacher,manager,employee,bus_supervisor']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $myDate =  $request->birthday;
        $date = Carbon::createFromFormat('m/d/Y', $myDate)->format('Y-m-d');
        $Employee->fullName = $request->fullName;
        $Employee->gender = $request->gender;
        $Employee->birthday = $date;
        $Employee->phone = $request->phone;
        $Employee->location = $request->location;
        $Employee->healthInfo = $request->healthInfo;
        $Employee->degree = $request->degree;
        $Employee->specialization=$request->specialization;
        $Employee->save();
        $res = collect();
        $res->push([
            'message' => 'added successfully',
            'data' => $Employee
        ]);
        return ['data' => $res, 'status' => 210];
    }

    public function destroy($id)
    {
        $Employee = Employee::findOrFail($id);
        $account = $Employee->owner;
        /*
        panding account status [use event|listener]
         */
        // return ['data' => $account, 'status' => 210];
        $account->status='suspended';
        $account->save();
    }
}
