<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Models\Answer;
use App\Models\Question;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Homework;

class HomeworkController extends Controller
{

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page_number'=>['required','numeric'],
            'description'=>['required','string'],
            'lesson_id'=>['required','exists:lessons,id'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $input=[
            'page_number'=>$request->page_number,
            'description'=>$request->description,
            'lesson_id'=>$request->lesson_id,
        ];
        $lesson=Homework::create($input);
        return ['data'=>$lesson,'status'=>210];
    }

    public function update(Request $request,  $id)
    {
        $homework=Homework::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'page_number'=>['required','numeric'],
            'description'=>['required','string'],
            'lesson_id'=>['required','exists:lessons,id'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $input=[
            'page_number'=>$request->page_number,
            'description'=>$request->description,
            'lesson_id'=>$request->lesson_id,
        ];
        $homework->update($request->all());
        return ['data'=>$homework,'status'=>210];
    }

    public function destroy($id)
    {
        Homework::destroy($id);
        return ['data'=>'deleted successfully','status'=>210];
    }
}
