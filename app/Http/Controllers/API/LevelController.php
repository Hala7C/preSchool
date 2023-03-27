<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class LevelController extends Controller
{
    public function index()
    {
        //
        $levels = Level::all();
        return ['data' => $levels, 'status' => '210'];
    }


    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'unique:level,name', 'alpha', 'max:255',],
            'age' => ['required', 'integer', 'between:4,6']
        ], [
            'name.unique' => 'This Level is Already Exists',
            'required' => 'The field (:attribute) is required ',

        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        } else {
            $input = [
                'name' => $request->name,
                'age' => $request->age,
            ];
            $level = Level::create($input);
            return ['data' => $level, 'status' => '210'];
        }
    }


    public function show($id)
    {
        $level = Level::findOrFail($id);
        return ['data' => $level, 'status' => '210'];
    }

    public function update(Request $request, $id)
    {
        $level = Level::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'name' => ['sometimes', 'required', 'alpha', 'max:255', Rule::unique('level', 'name')->ignore($level->id),],
            'age' => ['sometimes', 'required', 'integer', 'between:4,6']
        ], [
            'name.unique' => 'This Level is Already Exists',
            'required' => 'The field (:attribute) is required ',

        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $level->update($request->all());
        return ['data' => $level, 'status' => '210'];
    }


    public function destroy($id)
    {
        //
        Level::destroy($id);
        return ['message' => 'Level deleted successfly'];
    }
}
