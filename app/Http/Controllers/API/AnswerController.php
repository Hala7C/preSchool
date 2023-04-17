<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Models\Answer;
use Illuminate\Http\Request;
use Carbon\Carbon;
class AnswerController extends Controller
{
    public function store(Request $request,$quesId)
    {
        $validator = Validator::make($request->all(), [
            // 'correct_answer' => ['required','in:a,b,c,d,e'],
            'text'=>['required', 'max:255'],
            'img'=>['nullable','mimes:jpg,jpeg,png', 'max:1024', 'max:1024'],
            'symbol'=>['required','in:a,b,c,d,e'],
            // 'question_id'=>['required','exists:questions,id']
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $path=null;
        if($request->hasFile('img')){
            $photo =  $request->file('img');
            $newphoto = time() . $photo->getClientOriginalName();
            $photo->move('answers/img', $newphoto);
            $path= 'answers/img/' . $newphoto;
        }
            $input = [
                'text'=>$request->text,
                // 'correct_answer'     => $request->correct_answer,
                'img'     =>$path,
                'symbol'     => $request->symbol,
                'question_id'     => $quesId,
            ];
            $answer = Answer::create($input);
            return ['data' => $answer, 'status' => '210'];
    }

    public function update(Request $request,$id)
    {
        $answer=Answer::findOrFail($id);
        $validator = Validator::make($request->all(), [
               // 'correct_answer' => ['required','in:a,b,c,d,e'],
               'text'=>['required', 'max:255'],
               'img'=>['nullable','mimes:jpg,jpeg,png','max:1024'],
               'symbol'=>['required','in:a,b,c,d,e'],
            ]

        );
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        if($request->hasFile('img')){
            $uniqueid=uniqid();
            $orginal_name=$request->file('img')->getClientOriginalName();
            $size=$request->file('img')->getSize();
            $extention=$request->file('img')->getClientOriginalExtension();
            $name=Carbon::now()->format('Ymd').'_'.$uniqueid.'.'.$extention;
            $imagepath=url('/storage/uploads/imgs/'.$name);
            $path=$request->file('file')->storeAs('public/uploads/imgs/',$name);
        }
        $answer->update($request->all());
        return ['data' => $answer, 'status' => '210'];
    }


    public function destroy( $id)
    {
        Answer::destroy($id);
        return ['message' => 'answer deleted successfly'];
    }
}
