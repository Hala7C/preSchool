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
            $user=$u->ownerable;
            $teacher->push([
                'id'=>$user->id,
                'fullName'=>$user->fullName,
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

    public function allAssignDate(){
        $users=User::all()->where('role','=','teacher');
        $data =collect();
        foreach ($users as $u){
            $user=$u->ownerable;
            $clases=$user->classes();
            foreach($clases as $class){
                $subjects=DB::table('subject')
                ->join('teacher_class_subject','subject.id','=','teacher_class_subject.subject_id')
                ->where('teacher_class_subject.class_id','=',$class->id)
                ->where('teacher_class_subject.teacher_id','=',$user->id)
                ->distinct()
                ->get(['subject.*']);
                $info=['class_id'=>$class->id,
                        'class_name'=>$class->name,
                        'subjects'=>$subjects
                        ];
                $data->push([
                    'id'=>$user->id,
                    'fullName'=>$user->fullName,
                    'classes_subjects'=>$info
                ]);
            }

        }
    return ['data'=>$data,'status'=>210];
    }

    public function unAssignsubjectFromTeacher($classID,$tID,$sid){
        $tClass=TeacherClassSubject::where('teacher_id','=',$tID)->where('class_id','=',$classID)->where('subject_id','=',$sid)->get();
        if(count($tClass)==0){
            return ['data' =>'there is no entry with this data','status'=>210];
        }
        TeacherClassSubject::destroy($tClass[0]->id);
        return ['data' =>'deleted successfully','status'=>210];
    }

    public function unAssignAllsubjectFromTeacher($classID,$tID){
        $tClasses=TeacherClassSubject::where('teacher_id','=',$tID)->where('class_id','=',$classID)->get();
        if(count($tClasses)==0){
            return ['data' =>'there is no entry with this data','status'=>210];
        }
        // return $tClasses;
        foreach ($tClasses as $sub){
            TeacherClassSubject::destroy($sub->id);
        }
        return ['data' =>'deleted successfully','status'=>210];
    }

    public function teacherClasess(){
        $user=Auth::user();
        $teacher=$user->ownerable;
        $clases=$teacher->classes();
        if(count($clases)==0){
            return ['data'=>'this teacher do not assignted to any class yet','status'=>210];
        }
        return ['data'=>$clases,'status'=>210];
    }
}
