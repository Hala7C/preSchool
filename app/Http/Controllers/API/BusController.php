<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bus;
use Illuminate\Support\Facades\Validator;

class BusController extends Controller
{

    public function index()
    {
        $buses = Bus::all();
        if ($buses->isEmpty()) {
            return ['data' => 'there is no student', 'status' => '210'];
        }
        return ['data' => $buses, 'status' => '210'];
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'capacity' => 'required|integer',
            'number' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        } else {
            $input = [
                'capacity' => $request->capacity,
                'number'   => $request->number
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
}
