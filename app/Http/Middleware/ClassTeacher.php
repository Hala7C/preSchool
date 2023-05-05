<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Lesson;
class ClassTeacher
{

    public function handle(Request $request, Closure $next)
    {
        //check if auth teacher can change this class status
        $teacher=Auth::user()->ownerable;
        $cID=$request->route()->parameter('cID');
        $lID=$request->route()->parameter('lID');
        $lesson=Lesson::findOrFail($lID);
        $subject=$lesson->subject()->get();
        $Rteacher=DB::table('employee')
            ->join('teacher_class_subject','class.id','=','teacher_class_subject.class_id')
            ->where('teacher_class_subject.class_id','=',$cID)
            ->where('teacher_class_subject.subject_id','=',$subject->id)
            ->first(['employee.*']);
            if($teacher->id!=$Rteacher->id){
                return response()->json('you do not have the right to make changes on this class ',400);
            }
        return $next($request);
    }
}
