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
class AnswerController extends Controller
{
    public function store(Request $request,$quesId,$symbol)
    {
        $validator = Validator::make($request->all(), [
            'text'=>['required', 'max:255'],
            'img'=>['nullable','mimes:jpg,jpeg,png', 'max:1024'],
            // 'symbol'=>['required','in:a,b,c,d,e'],
            // 'question_id'=>['required','exists:questions,id']
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $path=null;
        if(isset($request->img)){
            $photo =  $request->input("img");
            $newphoto = time() . $photo->getClientOriginalName();
            $photo->move('answers/img', $newphoto);
            $path= 'answers/img/' . $newphoto;
        }
            $input = [
                'text'=>$request->text,
                // 'correct_answer'     => $request->correct_answer,
                'img'     =>$path,
                'symbol'     => $symbol,
                'question_id'     => $quesId,
            ];
            $answer = Answer::create($input);
            // return ['data' => $answer, 'status' => '210'];
    }

    public function update(Request $request,$quesId,$symbol)
    {
        $ques=Question::findOrFail($quesId);
        $answer=$ques->answers()->where('symbol',$symbol)->first();
        $validator = Validator::make($request->all(), [
            'text'=>['required', 'max:255'],
            'img'=>['nullable','mimes:jpg,jpeg,png', 'max:1024'],
            // 'symbol'=>['required','in:a,b,c,d,e'],
            // 'question_id'=>['required','exists:questions,id']
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $path=null;
        if(isset($request->img)){
            $photo =  $request->input("img");
            $newphoto = time() . $photo->getClientOriginalName();
            $photo->move('answers/img', $newphoto);
            $path= 'answers/img/' . $newphoto;
            $answer->img=$path;
        }

        $answer->text=$request->text;
        $answer->save();

    }


    public function destroy( $id)
    {
        $answer=Answer::findOrFail($id);
        if (File::exists($answer->img)) {
            File::delete(public_path($answer->img));
        }
        $answer->destroy($id);
        return ['message' => 'answer deleted successfly'];
    }
}
