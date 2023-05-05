<?php

namespace App\Http\Controllers\API;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Middleware\Teacher;
use App\Models\Classe;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\User;
use App\Models\TeacherClassSubject;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use function PHPUnit\Framework\isEmpty;
use function PHPUnit\Framework\isNull;

class TeacherController extends Controller
{

    public function allTeacher(){
        $users=User::all()->where('role','=','teacher');
        $teacher =collect();
        foreach ($users as $u){
            $teacher->push([
                $u->ownerable
            ]);
        }
        return ['data'=>$teacher,'status'=>210];
    }

    public function assignTeacherToClassWithSubjects(Request $request){
        $validator=Validator::make($request->all(),[
            'class_id'=>['required','exists:class,id'],
            'teacher_id'=>['required','exists:employee,id'],
            'subjects'=>['required']
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        $subjects=$request->subjects;
        // $i=0;
        //     $req=new Request($subjects);
        //     $validator=Validator::make($req->all(),[
        //         $i=>['required','exists:subject,id'],
        //     ]);
        //     if($validator->fails()){
        //         return response()->json($validator->errors(), 400);
        //     }

        foreach($subjects as $s){
            $input=[
                    'class_id'=>$request->class_id,
                    'teacher_id'=>$request->teacher_id,
                    'subject_id'=>$s
            ];
            TeacherClassSubject::create($input);
        }
        return ['data'=>'assignted successfully','status'=>210];
    }

    // public function assignTeacherToSubject(Request $request){
    //     $validator=Validator::make($request->all(),[
    //         'subject_id'=>['required','exists:subject,id'],
    //         'teacher_id'=>['required','exists:employee,id']
    //     ]);
    //     if($validator->fails()){
    //         return response()->json($validator->errors(), 400);
    //     }
    //     $subject=Subject::findOrFail($request->subject_id);
    //     $subject->teachers()->attach($request->teacher_id);
    //     return ['data'=>'assignted successfully','status'=>210];
    // }

    public function teacherClases($tID){
        $teacher=Employee::findOrFail($tID);
        $clases=$teacher->classes();
        if(count($clases)==0){
            return ['data'=>'this teacher do not assignted to any class yet','status'=>210];
        }
        return ['data'=>$clases,'status'=>210];
    }

    public function teacherSubjects( $Tid){
        $teacher=Employee::findOrFail($Tid);
        $subjects=$teacher->subjects($teacher->id);
        if(count($subjects)==0){
            return ['data'=>'this teacher do not assignted to any subject yet','status'=>210];
        }
        return ['data'=>$subjects,'status'=>210];
    }



    public function teacherSubjectinXClass($classID,$tID){
        $class=Classe::findOrFail($classID);
        // $user=Auth::user();
        // $teacher=$user->ownerable;
        $teacher=Employee::findOrFail($tID);
        $subjects=DB::table('subject')
        ->join('teacher_class_subject','subject.id','=','teacher_class_subject.subject_id')
        ->where('teacher_class_subject.class_id','=',$class->id)
        ->where('teacher_class_subject.teacher_id','=',$teacher->id)
        ->distinct()
        ->get(['subject.*']);
        return ['data' =>$subjects,'status'=>210];
    }

    public function SubjectTeachers( $id){
        $subject=Subject::findOrFail($id);
        $teachers=$subject->teachers();
        if(count($teachers)==0){
            return ['data'=>'this subject do not assignted to any teacher yet','status'=>210];
        }
        return ['data'=>$teachers,'status'=>210];
    }

    public function ClassTeachers($id){
        $class=Classe::findOrFail($id);
        $teachers=$class->teachers();
        if(count($teachers)==0){
            return ['data'=>'this class do not assignted to any teacher yet','status'=>210];
        }
        return ['data'=>$teachers,'status'=>210];
    }
}
