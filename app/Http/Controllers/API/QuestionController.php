<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Answer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Models\Quize;
use App\Models\Question;
use Carbon\Carbon;
use Illuminate\Http\Request;

class QuestionController extends Controller
{

    public function index()
    {
        $questions=Question::all();
        return ['data' => $questions, 'status' => '210'];
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'text'=>['required', 'max:255'],
            'audio'=>['nullable','file','mimes:audio/mpeg,mpga,wav,mp3', 'max:1024'],
            'category_id'=>['required','exists:categories,id'],
            'correct_answer_symbol'=>['required','in:a,b,c,d,e'],
            'answers' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
            $path=null;
            if($request->hasFile('audio')){
                $photo =  $request->file('audio');
                $newphoto = time() . $photo->getClientOriginalName();
                $photo->move('questions/audio', $newphoto);
                $path= 'questions/audio/' . $newphoto;
            }

            $input = [
                'text'     => $request->text,
                'audio'     => $path,
                'category_id'     => $request->category_id,

            ];
            $correct_answer=$request->correct_answer_symbol;
            $question = Question::create($input);
            $answers = $request->post('answers');
            foreach($answers as $answer){
                $request=new Request($answer);
              $ans=(new AnswerController)->store($request,$question->id);
            }
            $anss=$question->answers()->get();
            foreach($anss as $ans){
                if($ans->symbol==$correct_answer){

                    $ans->correct_answer=true;
                    $ans->save();
                break;
                }
            }
            return $this->show($question->id);

    }

    public function show( $id)
    {
        $question=Question::findOrFail($id);
        $answers=$question->answers()->get();
        $data=([
            'id'=>$question->id,
            'text'=>$question->text,
            'audio'=>$question->audio,
            'category_id'=>$question->category_id,
            'answers'=>$answers
        ]);
        return ['data' => $data, 'status' => '210'];

    }

    public function update(Request $request, $id)
    {
        $question=Question::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'text'=>['required', 'max:255'],
            'audio'=>['nullable','file','mimes:audio/mpeg,mpga,wav,mp3', 'max:1024'],
            'category_id'=>['required','exists:categories,id'],
            'correct_answer_symbol'=>['required','in:a,b,c,d,e'],
            'answers' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $path=null;
        if($request->hasFile('audio')){
            $photo =  $request->file('audio');
            $newphoto = time() . $photo->getClientOriginalName();
            $photo->move('questions/audio', $newphoto);
            $path= 'questions/audio/' . $newphoto;
        }

        $question->text=$request->text;
        $question->audio=$path;
        $question->category_id=$request->category_id;
        $question->save();

        $correct_answer=$request->correct_answer_symbol;
        $answers = $request->post('answers');
        foreach($answers as $answer){
            $request=new Request($answer);
          $ans=(new AnswerController)->update($request,$question->id);
        }
        $anss=$question->answers()->get();
        foreach($anss as $ans){
            if($ans->symbol==$correct_answer){
                $ans->correct_answer=true;
                $ans->save();
            break;
            }
        }
        return $this->show($question->id);
    }

    public function destroy($id)
    {
        Question::destroy($id);
        return ['message' => 'question deleted successfly'];
    }

    public function answers($id){
        $answers=Question::findOrFail($id)->with('answers')->get();
        return ['data' => $answers, 'status' => '210'];

    }
}
/***
 * {
    "text": "where is apple?",
    "category_id": "1",
    "correct_answer_symbol": "a",
    "answers" : [
        {
            "text" : "apple",
            "symbol" : "a"
        },
        {
            "text" : "orange",
            "symbol" : "b"
        },
        {
             "text" : "egg",
            "symbol" : "c"
        },
        {
             "text" : "lattuce",
            "symbol" : "d"
        }
    ]

}
 */
