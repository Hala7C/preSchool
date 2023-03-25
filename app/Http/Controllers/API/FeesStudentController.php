<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\FeesConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class FeesStudentController extends Controller
{
    public function index()
    {
        //
        $configs = FeesConfig::all();
        return ['data' => $configs, 'status' => '210'];
    }


    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'date' => ['required', 'date', Rule::unique('fees_config')->where(function ($query) use ($request) {
                return $query->where('date', $request->date)
                    ->where('amount', $request->amount);
            })],
            'amount' => ['required', 'integer'],
        ], [
            'date.unique' => ' On this date, there is a specific amount that must be paid. Select another time and amount :(',
            'required' => 'The field (:attribute) is required ',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        } else {
            $input = [
                'date' => $request->date,
                'amount' => $request->amount,
            ];
            $config = FeesConfig::create($input);
            return ['data' => $config, 'status' => '210'];
        }
    }


    public function show($id)
    {
        $config = FeesConfig::findOrFail($id);
        return ['data' => $config, 'status' => '210'];
    }


    public function update(Request $request, $id)
    {
        $config = FeesConfig::findOrFail($id);
        $request->validate([
            'date' => ['sometimes', 'required', 'date', Rule::unique('fees_config')->where(function ($query) use ($request) {
                return $query->where('date', $request->date)
                    ->where('amount', $request->amount);
            }),],
            'amount' => ['sometimes', 'required', 'integer'],
        ], [
            'date.unique' => ' On this date, there is a specific amount that must be paid. Select another time and amount :(',
            'required' => 'The field (:attribute) is required ',
        ]);

        $config->update($request->all());
        return ['data' => $config, 'status' => '210'];
    }


    public function destroy($id)
    {
        //
        FeesConfig::destroy($id);
        return ['message' => 'This config deleted successfly'];
    }
}
