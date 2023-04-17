<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{

    public function index()
    {
        $categories=Category::all();
        return ['data' => $categories, 'status' => '210'];
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'img'=>['nullable','mimes:jpg,jpeg,png', 'max:1024', 'max:1024'],
            'name' => ['required', 'alpha', 'max:255', Rule::unique('categories')->where(function ($query) use ($request) {
                return $query->where('name', $request->name);
            })],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        } else {
            $input = [
                'name'     => $request->name,
                'img'      =>$request->img,
            ];
            $category = Category::create($input);
            return ['data' => $category, 'status' => '210'];
        }
    }
    public function update(Request $request, $id)
    {
        $category=Category::findOrFail($id);
        $validator=Validator::make($request->all(),[
            'name' => ['sometimes','required', 'alpha', 'max:255'],
            'img'=>['nullable','mimes:jpg,jpeg,png', 'max:1024', 'max:1024'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $category->update($request->all());
        return ['data' => $category, 'status' => '210'];
    }

    public function destroy($id)
    {
        Category::destroy($id);
        return ['message' => 'category deleted successfly'];
    }

    public function categoryQuestions($id){
        $questions=Category::findOrFail($id)->with('quizes')->get();
        if($questions ==null){
        return ['data' => 'there is no questions yet for this category', 'status' => '210'];

        }
        return ['data' => $questions, 'status' => '210'];
    }
}
