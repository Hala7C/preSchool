<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|alpha|max:60',
                'level_id'=>'required|exists:level,id'
            ],
            [
                // 'name.unique' => 'This subject is already exists :(',
                'required' => 'The field (:attribute) is required ',
            ]
        );
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        } else {
            $input = [
                'name' => $request->name,
                'level_id'=>$request->level_id
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
        $validator = Validator::make(
            $request->all(),
            [

                'name' => ['sometimes', 'required', 'alpha', 'max:255',
                //  Rule::unique('subject', 'name')->ignore($subject->id),
            ],
                 'level_id'=>['sometimes','required','exist:level,id']

            ],
            [
                // 'name.unique' => 'This subject already exists  :(',
                'required' => 'The field (:attribute) is required ',
            ]
        );
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $subject->update($request->all());
        return ['data' => $subject, 'status' => '210'];
    }

    public function destroy($id)
    {
        //
        Subject::destroy($id);
        return ['message' => 'subject deleted successfly'];
    }

    public function subjectLessons($id){
        $subject=Subject::findOrFail($id);
        $lessons=$subject->lessons()->get();
        return ['data'=>$lessons,'status'=>210];
    }
}
