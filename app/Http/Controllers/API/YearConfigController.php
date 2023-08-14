<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\YearConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class YearConfigController extends Controller
{
    public function index()
    {
        //
        $configs = YearConfig::all();
        $data = collect();
        foreach ($configs as $config) {
            $data->push([
                'id' => $config->id,
                'year' => $config->year,
                'study_fees' => $config->study_fees,
                'bus_fees' => $config->bus_fees,
                'discount_bus' => intval($config->discount_bus),
                'discount_without_bus' => intval($config->discount_without_bus)
            ]);
        }
        return ['data' => $data, 'status' => '210'];
    }


    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'year' => 'required|unique:year_config,year',
            'study_fees' => 'required|integer',
            'bus_fees' => 'required|integer',
            'discount_bus' => 'required',
            'discount_without_bus' => 'required',
        ], ['required' => 'The field (:attribute) is required ',]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        } else {
            $input = [
                'year' => $request->year,
                'study_fees' => $request->study_fees,
                'bus_fees' => $request->bus_fees,
                'discount_bus' => $request->discount_bus,
                'discount_without_bus' => $request->discount_without_bus,
            ];
            $config = YearConfig::create($input);
            return ['data' => $config, 'status' => '210'];
        }
    }


    public function show($id)
    {
        $config = YearConfig::findOrFail($id);
        return ['data' => $config, 'status' => '210'];
    }


    public function update(Request $request, $id)
    {
        $config = YearConfig::findOrFail($id);
        $request->validate([

            'year' => 'sometimes|required',
            'study_fees' => 'sometimes|required|integer',
            'bus_fees' => 'sometimes|required|integer',
            'discount_bus' => 'sometimes|required',
            'discount_without_bus' => 'sometimes|required',

        ], ['required' => 'The field (:attribute) is required ',]);

        $config->update($request->all());
        return ['data' => $config, 'status' => '210'];
    }


    public function destroy($id)
    {
        //
        YearConfig::destroy($id);
        return ['message' => 'This config deleted successfly'];
    }
}
