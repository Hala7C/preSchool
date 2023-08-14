<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Middleware\Student;
use App\Models\Mark;
use App\Models\StudentClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class MarkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $exam_id = $request->exam_id;
        $class_id = $request->class_id;
        $std_ids = StudentClass::where("class_id", $class_id)->pluck('student_id')->toArray();
        $marks = Mark::with("student")->where("exam_id", $exam_id)->whereIn('student_id', $std_ids)->get();
        return ['data' => $marks, 'status' => 210];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function insertMarket(Request $request)
    {
        $exam_id = $request->exam_id;

        $validator = Validator::make($request->all(), [
            'exam_id' => ['required', 'exists:exams,id'],
            'student_marks' => ['required', 'array'],

        ], [
            'required' => 'The field (:attribute) is required ',

        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        } else {
            $student_marks = $request->post("student_marks");
            $marks_st = Mark::where('exam_id', $exam_id)
                ->pluck('student_id')
                ->toArray();
            foreach ($student_marks as $student_id => $mark) {

                if (!in_array($student_id, $marks_st)) {
                    Mark::create([
                        "student_id" => $student_id,
                        "exam_id"  => $exam_id,
                        "mark" => $mark,
                    ]);
                } else {
                    continue;
                }
            }

            $data = "All student marks have been entered successfully :)";
            return ['data' => $data, 'status' => '210'];
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($student_id)
    {
        $marks = Mark::where('student_id', $student_id)->with("student")->get();
        return ['data' => $marks, 210];
    }


    public function update(Request $request, $id)
    {
        //
        $mark = Mark::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'mark' => ['required', 'integer']
        ], [
            'integer' => 'The mark must be integer',
            'required' => 'The field (:attribute) is required ',

        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        } else {
            DB::table('marks')->where('id', $id)->update(['mark' => $request->mark]);
        }
        return ['data' => $mark, 210];
    }


    public function destroy($id)
    {
        //
        Mark::destroy($id);
        return ['message' => 'this mark deleted successfly'];
    }
}
