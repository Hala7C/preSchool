<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Classe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ClassController extends Controller
{

    public function index()
    {
        //
        $classes = Classe::all();
        return ['data' => $classes, 'status' => '210'];
    }


    public function store(Request $request)
    {
        //

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'alpha', 'max:255', Rule::unique('class')->where(function ($query) use ($request) {
                return $query->where('name', $request->name)
                    ->where('level_id', $request->level_id);
            })],
            'capacity' => ['required', 'integer'],
            'level_id' => ['required', 'exists:level,id'],
        ], [
            'name.unique' => 'This name of class is already exists in this level :(',
            'required' => 'The field (:attribute) is required ',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        } else {
            $input = [
                'name'     => $request->name,
                'capacity' => $request->capacity,
                'level_id' => $request->level_id,
            ];
            $class = Classe::create($input);
            return ['data' => $class, 'status' => '210'];
        }
    }


    public function show($id)
    {
        $class = Classe::findOrFail($id);
        return ['data' => $class, 'status' => '210'];
    }


    public function update(Request $request, $id)
    {
        $class = Classe::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => ['sometimes', 'required', 'alpha', 'max:255', Rule::unique('class')->where(function ($query) use ($request) {
                return $query->where('name', $request->name)
                    ->where('level_id', $request->level_id);
            })->ignore($class->id)],
            'capacity' => ['sometimes', 'required', 'integer'],
            'level_id' => ['sometimes', 'required', 'exists:level,id'],
        ], [
            'name.unique' => 'This name of class is already exists in this level :(',
            'required' => 'The field (:attribute) is required ',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $class->update($request->all());
        return ['data' => $class, 'status' => '210'];
    }


    public function destroy($id)
    {
        //
        Classe::destroy($id);
        return ['message' => 'class deleted successfly'];
    }
}
