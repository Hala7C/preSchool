<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\FeesConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
            'date' => 'required|date_format:dd/mm/yyyy',
            'amount' => 'required|integer',
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

            'date'   => 'sometimes|required|date',
            'amount' => 'sometimes|required|integer'

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
