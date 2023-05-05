<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Http\Requests\LessonRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Lesson;
use App\Models\Level;
use App\Models\Subject;
use Illuminate\Http\Request;

use function PHPUnit\Framework\isEmpty;

class LessonController extends Controller
{

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'=>['required','string','unique:lessons,title'],
            'semester'=>['required','in:s1,s2,undefined'],
            'number'=>['nullable','numeric'],
            'subject_id'=>['required','exists:subject,id']
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $input=[
            'title'=>$request->title,
            'semester'=>$request->semester,
            'number'=>$request->number,
            'subject_id'=>$request->subject_id
        ];
        $lesson=Lesson::create($input);

        //add lesson to all classes with ungiven status
        $subject=Subject::findOrFail($request->subject_id);
        $level=$subject->level()->get();
        $levelID=$level[0]['id'];
        $l=Level::find($levelID);
        $classes=$l->classes()->get();
        foreach($classes as $class){
            $class->lessons()->attach($lesson->id,['status'=>'ungiven']);
        }
        return ['data'=>$lesson,'status'=>210];
    }

    public function update(Request $request,$id)
    {
        $lesson=Lesson::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'title'=>['required','string',Rule::unique('lessons','title')->ignore($id)],
            'semester'=>['required','in:s1,s2,undefined'],
            'number'=>['nullable','numeric'],
            'subject_id'=>['required','exists:subject,id']
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $lesson->update($request->all());
        return ['data'=>$lesson,'status'=>210];
    }

    public function destroy( $id)
    {

        $data=Lesson::destroy($id);
        return ['data'=>'deleted successfully','status'=>210];
    }

    public function homeworks($id){
        $l=Lesson::findOrFail($id);
        $homeworks=$l->homeworks()->get();
        if(count($homeworks)==0){
            return ['msg'=>'there is no homeworks for this lesson yet'];
        }
        return ['data'=>$homeworks,'status'=>210];
    }

    public function lessonStatus($cID,$lID)
    {
        $lesson=Lesson::findOrFail($lID);
            //change status
            DB::table('lesson_class')
                        ->where('lesson_id',$lID)
                        ->where('class_id',$cID)
                        ->limit(1)
                        ->update(array('status'=>'given'));

                // $lessonStatus=$lesson->classes()->updateExistingPivot($cID,['status'=>'given']);
            //check if this lesson has a homework
            $homeworksCount=$lesson->homeworks()->count();
            $homeworks=$lesson->homeworks()->get();
            if($homeworksCount != 0){
                return ['data'=>$homeworks,'status'=>210];
            }
            return ['data'=>'there are no homeworks for this lesson'];
    }



    public function lessonStatus1($lID)
    {
        $lesson=Lesson::findOrFail($lID);
        $subject=$lesson->subject()->get();
        $teacher=Auth::user()->ownerable;;
        $class=DB::table('class')
        ->join('teacher_class_subject','subject.id','=','teacher_class_subject.subject_id')
        ->where('teacher_class_subject.subject_id','=',$subject->id)
        ->where('teacher_class_subject.teacher_id','=',$teacher->id)
        ->distinct()
        ->get(['class.*']);
        $cID=$class->id;
            //change status
            DB::table('lesson_class')
                        ->where('lesson_id',$lID)
                        ->where('class_id',$cID)
                        ->limit(1)
                        ->update(array('status'=>'given'));
                // $lessonStatus=$lesson->classes()->updateExistingPivot($cID,['status'=>'given']);
            //check if this lesson has a homework
            $homeworksCount=$lesson->homeworks()->count();
            $homeworks=$lesson->homeworks()->get();
            if($homeworksCount != 0){
                return ['data'=>$homeworks,'status'=>210];
            }
            return ['data'=>'there are no homeworks for this lesson'];
    }

    public function sendHomework($lessonID){
        //send homework for all students in this class
        //send advertisment on class level with assignment info

    }
}
