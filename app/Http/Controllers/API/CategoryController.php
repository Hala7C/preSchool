<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\Models\Category;
use Collator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use function PHPUnit\Framework\isEmpty;

class CategoryController extends Controller
{

    public function index()
    {
        $categories = Category::all();
        $data = Collect();

        foreach ($categories as $category) {
            $s = explode('/', $category->img);
            $data->push([
                'id' => $category->id,
                'name' => $category->name,
                'img_name'    => $s[2],
                'img' => $category->img

            ]);
        }

        return ['data' => $data, 'status' => '210'];
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'img' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
            'name' => ['required', 'alpha', 'max:255', Rule::unique('categories')->where(function ($query) use ($request) {
                return $query->where('name', $request->name);
            })],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $path = null;
        if ($request->hasFile('img')) {
            $photo =  $request->file("img");
            $newphoto = time() . $photo->getClientOriginalName();
            $photo->move('categories/img', $newphoto);
            $path = 'categories/img/' . $newphoto;
        }

        $input = [
            'name'     => $request->name,
            'img'      => $path,
        ];
        $category = Category::create($input);
        $data = collect();
        $s = explode('/', $category->img);
        $data = [
            'id' => $category->id,
            'name' => $category->name,
            'img_name'    => $s[2],
            'img' => $category->img
        ];
        return ['data' => $data, 'status' => '210'];
    }
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'name' => ['sometimes', 'required', 'alpha', 'max:255'],
            'img' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024', 'max:1024'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        // $path = null;
        if ($request->hasFile('img')) {
            $photo =  $request->file("img");
            $newphoto = time() . $photo->getClientOriginalName();
            $photo->move('categories/img', $newphoto);
            $path = 'categories/img/' . $newphoto;
            $category->img = $path;
        }
        $category->name = $request->name;
        $category->save();
        $data = collect();
        $s = explode('/', $category->img);
        $data = [
            'id' => $category->id,
            'name' => $category->name,
            'img_name'    => $s[2],
            'img' => $category->img
        ];
        return ['data' => $data, 'status' => '210'];
    }

    public function destroy($id)
    {
        Category::destroy($id);
        return ['message' => 'category deleted successfly'];
    }

    public function categoryQuestions($id)
    {
        $category = Category::findOrFail($id);
        $questions = $category->questions()->get();
        if (count($questions) == 0) {
            return ['data' => [], 'status' => '210'];
        }
        $questions = $category->questions()->get();
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

    public function categoryQuestionsStudent($id)
    {
        $category = Category::findOrFail($id);
        $questions = $category->questions()->count();
        if ($questions == 0) {
            return ['data' => [], 'status' => '210'];
        }
        $questions = $category->questions()->get();
        $data = collect();
        foreach ($questions as $question) {
            $correct_answer = $question->answers()->where('correct_answer', true)->first();
            $data->push([
                'id' => $question->id,
                'text' => $question->text,
                'audio' => $question->audio,
                'category_id' => $question->category_id,
                'answers' => $question->answers()->get(),
                'correct_answer_text' => $correct_answer->text,
                "correct_answer_symbol" => $correct_answer->symbol
            ]);
        }
        return ['data' => $data, 'status' => '210'];
    }
}
