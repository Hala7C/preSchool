<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Classe;
use App\Models\Student;
use App\Models\StudentClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isNull;

class AssignStudentsToClassController extends Controller
{

    public function StudentNotAssigned()
    {
        $students=Student::all();
        $data=collect();
        $i=0;
        foreach($students as $std){
            if( $std->withCount('class')->get()[$i]->class_count == 0){
                $data->push([
                    'id'=>$std->id,
                    'name'=>$std->fullName
                ]);
            }
            ++$i;
        }

        if($data->count()==0){
            return ['data'=>'All Students have been assignded','status'=>210];

        }
        return ['data'=>$data,'status'=>210];

    }

    public function store($classID,Request $request)
    {
        $class=Classe::findOrFail($classID);
        $validator=Validator::make($request->all(),[
            'students' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $students=$request->students;
        $studentsCount=count($students);
        if($studentsCount > $class->capacity){
            return ['data'=>'student number more than class capacity ','status'=>210];
        }
        DB::beginTransaction();
        foreach($students as $std){
            if ( Student::where('id', '=', $std)->exists()) {
                StudentClass::create([
                    'student_id'=>$std,
                    'class_id'=>$classID
                ]);
            }else{
                DB::rollBack();
                return ['data'=>'there is no student with id '.$std,'status'=>210];
            }
        }
        DB::commit();
        ['data'=>'added successfully','status'=>210];
    }


    public function show($classID)
    {
        $class=Classe::findOrFail($classID);
        $students=$class->students()->get();
        if(count($students)==0){
            return ['data'=>'No students assign to this class yet','status'=>210];
        }
        $data=collect();
        foreach($students as $std){
            $student=Student::findOrFail($std->student_id);
            $data->push([
                'id'=>$student->id,
                'name'=>$student->fullName
            ]);
        }
        return ['data'=>$data,'status'=>210];
    }


    public function deleteStudentFromClass(Request $request, $classID)
    {
        $class=Classe::findOrFail($classID);
        $validator=Validator::make($request->all(),[
            'students' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $students=$request->students;
        DB::beginTransaction();
        foreach($students as $std){
            if ( Student::where('id', '=', $std)->exists()) {
                $stdClass=StudentClass::where('student_id','=',$std)->where('class_id','=',$classID)->get();
                if(count($stdClass)==0){
                    DB::rollBack();
                    return ['data'=>'there is no student with id '.$std,'status'=>210];
                }
                StudentClass::destroy($stdClass[0]->id);
            }else{
                DB::rollBack();
                return ['data'=>'there is no student with id '.$std,'status'=>210];
            }
        }
        DB::commit();
        return ['data'=>'updated successfully','status'=>210];
    }

}
