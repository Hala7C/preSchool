<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubjectController extends Controller
{

    public function index()
    {
        //
        $subjects = Subject::all();
        return ['data' => $subjects, 'status' => '210'];
    }

    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'name' => 'required|alpha|max:255',

        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        } else {
            $input = [
                'name' => $request->name,
                'age' => $request->age,
            ];
            $subject = Subject::create($input);
            return ['data' => $subject, 'status' => '210'];
        }
    }


    public function show($id)
    {
        $subject = Subject::findOrFail($id);
        return ['data' => $subject, 'status' => '210'];
    }


    public function update(Request $request, $id)
    {
        $subject = Subject::findOrFail($id);
        $request->validate([

            'name' => 'sometimes|required|alpha|max:255',

        ]);

        $subject->update($request->all());
        return ['data' => $subject, 'status' => '210'];
    }

    public function destroy($id)
    {
        //
        Subject::destroy($id);
        return ['message' => 'subject deleted successfly'];
    }
}
