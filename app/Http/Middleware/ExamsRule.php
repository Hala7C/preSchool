<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class ExamsRule
{

    public function handle(Request $request, Closure $next)
    {
        $authTeacherID=Auth::user()->ownerable->id;
        $subID= $request->get('subject_id');
        $Rteacher=DB::table('employee')
            ->join('teacher_class_subject','employee.id','=','teacher_class_subject.teacher_id')
            ->where('teacher_class_subject.subject_id','=',$subID)
            ->where('teacher_class_subject.teacher_id','=',$authTeacherID)
            ->distinct()
            ->first('employee.*');
            if(empty($Rteacher)){
                return response()->json('you do not have the right to add exam to this subject ',400);
            }

        return $next($request);
    }
}
