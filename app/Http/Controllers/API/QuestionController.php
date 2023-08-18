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
use Collator;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

class QuestionController extends Controller
{

    public function index()
    {
        $questions = Question::all();
        $data = collect();
        foreach ($questions as $question) {
            $correct_answer = $question->answers()->where('correct_answer', true)->first();
            $data->push([
                'id' => $question->id,
                'text' => $question->text,
                'audio' => $question->audio,
                'category_id' => $question->category_id,
                'correct_answer_text' => $correct_answer->text,
                "correct_answer_symbol" => $correct_answer->symbol
            ]);
        }
        return ['data' => $data, 'status' => '210'];
    }



    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'text' => ['required', 'max:255'],
            'audio' => ['nullable', 'file', 'mimes:audio/mpeg,mpga,wav,mp3', 'max:1024'],
            'category_id' => ['required', 'exists:categories,id'],
            'correct_answer_symbol' => ['required', 'in:a,b,c,d,e'],
            'answers' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $symbols = ['a', 'b', 'c', 'd', 'e'];
        $path = null;
        if ($request->hasFile('audio')) {
            $photo =  $request->file('audio');
            $newphoto = time() . $photo->getClientOriginalName();
            $photo->move('questions/audio', $newphoto);
            $path = 'questions/audio/' . $newphoto;
        }
        $input = [
            'text'     => $request->text,
            'audio'     => $path,
            'category_id'     => $request->category_id,
        ];
        DB::beginTransaction();

        $question = Question::create($input);

        $correct_answer = $request->correct_answer_symbol;
        $answers = $request->answers;
        $i = 0;
        foreach ($answers as $answer) {
            $request = new Request($answer);
            $ans1 = (new AnswerController)->store($request, $question->id, $symbols[$i]);
            $i++;
            if ($ans1 != null) {
                DB::rollBack();
                return $ans1;
            }
        }
        $anss = $question->answers()->get();
        foreach ($anss as $ans) {
            if ($ans->symbol == $correct_answer) {
                $ans->correct_answer = true;
                $ans->save();
                break;
            }
        }
        DB::commit();
        $correct_answer = $question->answers()->where('correct_answer', true)->first();
        $data = [
            'id' => $question->id,
            'text' => $question->text,
            'audio' => $question->audio,
            'category_id' => $question->category_id,
            'correct_answer_text' => $correct_answer->text,
            "correct_answer_symbol" => $correct_answer->symbol
        ];
        return ['data' => $data, 'status' => '210'];
        // return $this->show($question->id);
    }

    public function show($id)
    {
        $question = Question::findOrFail($id);
        $answers = $question->answers()->get();
        $data = array();
        $correctSymbol = Answer::where('question_id', '=', $id)->where('correct_answer', true)->first();
        $ans = collect();
        foreach ($answers as $answer) {
            $s = explode('/', $answer->img);
            $ans->push([
                'id' => $answer->id,
                'text' => $answer->text,
                'img' => $answer->img,
                'img_name' => $s[2],
                'symbol' => $answer->symbol,
                'correct_answer' => $answer->correct_answer,
                'question_id' => $answer->question_id
            ]);
        }
        array_push($data, [
            'id' => $question->id,
            'text' => $question->text,
            'audio' => $question->audio,
            'category_id' => $question->category_id,
            'correct_Symbol' => $correctSymbol->symbol,
            'answers' => $ans
        ]);
        return ['data' => $data, 'status' => '210'];
    }

    public function update(Request $request, $id)
    {
        $question = Question::findOrFail($id);
        $symbols = ['a', 'b', 'c', 'd', 'e'];
        $validator = Validator::make($request->all(), [
            'text' => ['sometimes', 'required', 'max:255'],
            'audio' => ['nullable', 'file', 'mimes:audio/mpeg,mpga,wav,mp3', 'max:1024'],
            'category_id' => ['sometimes', 'required', 'exists:categories,id'],
            'correct_answer_symbol' => ['sometimes', 'required', 'in:a,b,c,d,e'],
            'answers' => ['sometimes', 'required'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $path = null;
        if ($request->hasFile('audio')) {
            $photo =  $request->file('audio');
            $newphoto = time() . $photo->getClientOriginalName();
            $photo->move('questions/audio', $newphoto);
            $path = 'questions/audio/' . $newphoto;
            $question->audio = $path;
        }


        $question->text = $request->text;
        $question->category_id = $request->category_id;
        DB::beginTransaction();

        $question->save();

        $correct_answer = $request->correct_answer_symbol;
        $answers = $request->answers;
        $i = 0;
        foreach ($answers as $answer) {
            $request = new Request($answer);
            $ans1 = (new AnswerController)->update($request, $question->id, $symbols[$i]);
            $i++;
            if ($ans1 != null) {
                DB::rollBack();
                $i = 0;
                return $ans1;
            }
        }
        $anss = $question->answers()->get();
        foreach ($anss as $ans) {
            if ($ans->symbol == $correct_answer) {
                $ans->correct_answer = true;
                $ans->save();
            } else {
                $ans->correct_answer = false;
                $ans->save();
            }
        }
        DB::commit();
        $correct_answer = $question->answers()->where('correct_answer', true)->first();
        $data = [
            'id' => $question->id,
            'text' => $question->text,
            'audio' => $question->audio,
            'category_id' => $question->category_id,
            'correct_answer_text' => $correct_answer->text,
            "correct_answer_symbol" => $correct_answer->symbol
        ];
        return ['data' => $data, 'status' => '210'];
        // return $this->show($question->id);
    }

    public function destroy($id)
    {
        $question = Question::findOrFail($id);
        if (File::exists($question->audio)) {
            File::delete(public_path($question->audio));
        }
        $answers = $question->answers()->get();
        foreach ($answers as $ans) {
            (new AnswerController)->destroy($ans->id);
        }
        $question->destroy($id);

        return ['message' => 'question deleted successfly'];
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
