<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bus;
use App\Models\Employee;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Pusher\Pusher;

class BusController extends Controller
{

    public function index()
    {
        $buses = Bus::all();
        if ($buses->isEmpty()) {
            return ['data' => 'there is no bus', 'status' => '210'];
        }
        return ['data' => $buses, 'status' => '210'];
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'capacity' => ['required|integer'],
            'number' => ['required|integer'],
            'bus_supervisor_id' => ['required']
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $emp = Employee::findOrFail($request->bus_supervisor_id);
        if ($emp->bus != null) {
            $data = collect();
            $data->push([
                'msg' => 'the supervisor is already assignmented to another bus'
            ]);
            return response()->json($data, 400);
        } else {

            $input = [
                'capacity' => $request->capacity,
                'number'   => $request->number,
                'bus_supervisor_id' => $request->bus_supervisor_id
            ];
            $class = Bus::create($input);
            return ['data' => $class, 'status' => '210'];
        }
    }

    public function update(Request $request, $id)
    {
        $bus = Bus::findOrFail($id);
        $validator = Validator::make($request->all(), [
            [
                'number'     => ['required|numeric'],
                'capacity' => ['required|numeric'],
                'bus_supervisor_id' => ['somtimes|required']
            ]
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $bus->update($request->all());
        return ['data' => $bus, 'status' => '210'];
    }


    public function destroy($id)
    {
        Bus::destroy($id);
        return ['data' => 'bus deleted successfly', 'status' => 210];
    }

    public function allBusSupervisor()
    {
        $supervisors_account = User::where('role', '=', 'bus_supervisor')->get();
        $data = collect();
        foreach ($supervisors_account as $supervisor) {
            $emp = $supervisor->ownerable;
            // return $emp;

            if ($emp->bus() != null) {
                $data->push([
                    'id' => $supervisor->id,
                    'name' => $emp->fullName
                ]);
            }
        }
        return ['data' => $data, 'status' => '210'];
    }
    public function allStudent($id)
    {
        $student = Student::find($id);
        if (is_null($student)) {
            return ['data' => [], 'status' => 200];
        }
        $bus = $student->bus()->first();
        if ($bus == null) {
            return ['data' => [], 'status' => '210'];
        }
        $students = $bus->students()->get();
        $data = collect();
        foreach ($students as $std) {
            $data->push([
                'id' => $std->id,
                'name' => $std->fullName,
                'lng' => $std->lng,
                'lat' => $std->lat
            ]);
        }
        return ['data' => $data, 'status' => '210'];
    }

    public function SupervisorAllStudent($id)
    {
        $supervisor = Employee::find($id);
        if (is_null($supervisor)) {
            return ['data' => [], 'status' => 200];
        }
        $bus = $supervisor->bus()->first();
        if ($bus == null) {
            return ['data' => [], 'status' => '210'];
        }
        $students = $bus->students()->get();
        $data = collect();
        foreach ($students as $std) {
            $data->push([
                'id' => $std->id,
                'name' => $std->fullName,
                'lng' => $std->lng,
                'lat' => $std->lat
            ]);
        }
        return ['data' => $data, 'status' => '210'];
    }

    public function allBusStudent()
    {
        $busses = Bus::all();
        $data = collect();
        foreach ($busses as $b) {
            $data->push([
                'bus_id' => $b->id,
                'students_list' => $b->students()->get()
            ]);
        }
        return ['data' => $data, 'status' => '210'];
    }


    public function allStudentWithoutBus()
    {
        $students = Student::all()->where('bus_registry', '=', '0');
        if ($students == null) {
            return ['data' => "students are not assigned to  buses yet !!\n please try again after sorting", 'status' => '210'];
        }
        $data = collect();
        foreach ($students as $std) {
            $data->push([
                'id' => $std->id,
                'name' => $std->fullName,
            ]);
        }
        return ['data' => $data, 'status' => '210'];
    }
}
