<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bus;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

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
            'capacity' => 'required|integer',
            'number' => 'required|integer',
            'bus_supervisor_id'=>'required'
        ]);
        $emp=Employee::findOrFail($request->bus_supervisor_id);
        $bus=Bus::findOrFail(1);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }elseif($emp->bus!=null){
            return response()->json('the supervisor is already assignmented to another bus', 400);
        } else {

            $input = [
                'capacity' => $request->capacity,
                'number'   => $request->number,
                'bus_supervisor_id'=>$request->bus_supervisor_id
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
                'number'     => 'required|numeric',
                'capacity' => 'required|numeric',
                'bus_supervisor_id'=>'somtimes|required'
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
        return ['message' => 'class deleted successfly'];
    }

    public function allBusSupervisor(){
        $supervisors_account=User::where('role','=','bus_supervisor')->get();
        $data=collect();
        foreach($supervisors_account as $supervisor){
            $emp=$supervisor->ownerable;
            if($emp->bus==null){
                $data->push([
                    'id'=>$supervisor->id,
                    'name'=>$emp->fullName
                ]);
        }
    }
        return ['data' => $data, 'status' => '210'];

    }
    public function allStudent($id){
        $bus=Bus::find($id);
        $students=$bus->students()->get();
        return ['data' => $students, 'status' => '210'];

    }

    public function allBusStudent(){
        $busses=Bus::all();
        $data =collect();
        foreach($busses as $b){
            $data->push([
                'bus_id'=>$b->id,
                'students_list'=>$b->students()->get()
            ]);
        }
        return ['data' => $data, 'status' => '210'];

    }
}
