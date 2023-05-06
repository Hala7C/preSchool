<?php

namespace App\Http\Controllers\API;

use App\Models\School;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Validator;


class SchoolController extends Controller
{

    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'name' => ['required', 'alpha', 'max:255'],
    //         'lng' => ['required', 'numeric'],
    //         'lat' => ['required', 'numeric'],
    //         'phone'=>['required','digits:10']
    //     ]);
    //     if ($validator->fails()) {
    //         return response()->json($validator->errors(), 400);
    //     } else {
    //         $input = [
    //             'name'     => $request->name,
    //             'lng' => $request->lng,
    //             'lat' => $request->lat,
    //             'phone'=>$request->phone
    //         ];
    //         $school = School::create($input);
    //         return ['data' => $school, 'status' => '210'];
    //     }
    // }


    public function show($id)
    {
        $school=School::findOrFail($id);
        return ['data' => $school, 'status' => '210'];
    }


    public function update(Request $request, $id)
    {
        $school=School::findOrFail($id);
        $validator=Validator::make($request->all(),[
            'name' => ['sometimes','required', 'alpha', 'max:255'],
            'start_time'=>['sometimes','required'],
            'lng' => ['sometimes','required', 'numeric'],
            'lat' => ['sometimes','required', 'numeric'],
            'phone'=>['sometimes','required','digits:10']
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $school->update($request->all());
        return ['data' => $school, 'status' => '210'];
    }

    public function updatelocation(Request $request, $id)
    {
        $school=School::findOrFail($id);
        $validator=Validator::make($request->all(),[
            'lng' => ['sometimes','required', 'numeric'],
            'lat' => ['sometimes','required', 'numeric'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $school->lng=$request->lng;
        $school->lat=$request->lat;
        $school->save();
        return ['data' => $school, 'status' => '210'];
    }


    // public function destroy($id)
    // {
    //     School::destroy($id);
    //     return ['message' => 'school deleted successfly'];
    // }
}
