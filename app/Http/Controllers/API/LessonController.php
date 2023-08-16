<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LessonRequest;
use App\Models\Classe;
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
use function PHPUnit\Framework\isNan;
use function PHPUnit\Framework\isNull;

class LessonController extends Controller
{

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'unique:lessons,title'],
            'semester' => ['required', 'in:s1,s2,undefined'],
            'number' => ['nullable', 'numeric'],
            'subject_id' => ['required', 'exists:subject,id']
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $input = [
            'title' => $request->title,
            'semester' => $request->semester,
            'number' => $request->number,
            'subject_id' => $request->subject_id
        ];
        $lesson = Lesson::create($input);

        //add lesson to all classes with ungiven status
        $subject = Subject::findOrFail($request->subject_id);
        $level = $subject->level()->get();
        $levelID = $level[0]['id'];
        $l = Level::find($levelID);
        $classes = $l->classes()->get();
        foreach ($classes as $class) {
            $class->lessons()->attach($lesson->id, ['status' => 'ungiven']);
        }
        return ['data' => $lesson, 'status' => 210];
    }

    public function update(Request $request, $id, $cid)
    {
        $lesson = Lesson::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'title' => ['sometimes', 'required', 'string', 'unique:lessons,title'],
            'semester' => ['sometimes', 'required', 'in:s1,s2,undefined'],
            'number' => ['nullable', 'numeric'],
            'subject_id' => ['sometimes', 'required', 'exists:subject,id'],
            'status' => ['sometimes', 'in:given,ungiven']
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $lesson->update($request->all());
        DB::table('lesson_class')
            ->where('lesson_id', $lesson->id)
            ->where('class_id', $cid)
            ->limit(1)
            ->update(array('status' => $request->status));
        return ['data' => $lesson, 'status' => 210];
    }

    public function destroy($id)
    {

        $data = Lesson::destroy($id);
        return ['data' => 'deleted successfully', 'status' => 210];
    }

    public function homeworks($id)
    {
        $l = Lesson::findOrFail($id);
        $homeworks = $l->homeworks()->get();
        if (count($homeworks) == 0) {
            return ['data' => [], 'status' => 210];
        }
        return ['data' => $homeworks, 'status' => 210];
    }

    public function subjectLessons($id, $cid)
    {

        $subject = Subject::findOrFail($id);
        $lessons = $subject->lessons()->get();

        $data = collect();
        foreach ($lessons as $lesson) {
            $status = DB::table('lesson_class')
                ->where('lesson_id', $lesson->id)
                ->where('class_id', $cid)
                ->limit(1)
                ->first();

            $data->push([

                'id' => $lesson->id,
                'title' => $lesson->title,
                'semester' => $lesson->semester,
                'number' => $lesson->number,
                'subject_id' => $lesson->subject_id,
                'status' => $status->status
            ]);
        }
        return ['data' => $data, 'status' => 210];
    }
    public function lessonStatus($cID, $lID)
    {
        $lesson = Lesson::findOrFail($lID);
        //change status
        DB::table('lesson_class')
            ->where('lesson_id', $lID)
            ->where('class_id', $cID)
            ->limit(1)
            ->update(array('status' => 'given'));

        // $lessonStatus=$lesson->classes()->updateExistingPivot($cID,['status'=>'given']);
        //check if this lesson has a homework
        $homeworksCount = $lesson->homeworks()->count();
        $homeworks = $lesson->homeworks()->get();
        if ($homeworksCount != 0) {
            return ['data' => $homeworks, 'status' => 210];
        }
        return ['data' => [], 'status' => 210];
    }



    public function lessonStatus1($lID)
    {
        $lesson = Lesson::findOrFail($lID);
        $subject = $lesson->subject()->get();
        $teacher = Auth::user()->ownerable;;
        $class = DB::table('class')
            ->join('teacher_class_subject', 'subject.id', '=', 'teacher_class_subject.subject_id')
            ->where('teacher_class_subject.subject_id', '=', $subject->id)
            ->where('teacher_class_subject.teacher_id', '=', $teacher->id)
            ->distinct()
            ->get(['class.*']);
        $cID = $class->id;
        //change status
        DB::table('lesson_class')
            ->where('lesson_id', $lID)
            ->where('class_id', $cID)
            ->limit(1)
            ->update(array('status' => 'given'));
        // $lessonStatus=$lesson->classes()->updateExistingPivot($cID,['status'=>'given']);
        //check if this lesson has a homework
        $homeworksCount = $lesson->homeworks()->count();
        $homeworks = $lesson->homeworks()->get();
        if ($homeworksCount != 0) {
            return ['data' => $homeworks, 'status' => 210];
        }
        return ['data' => [], 'status' => 210];
    }

    public function sendHomework($lessonID)
    {
        $lesson = Lesson::findOrFail($lessonID);
        $subject = $lesson->subject()->get();
        $teacher = Auth::user()->ownerable;;
        $class = DB::table('class')
            ->join('teacher_class_subject', 'class.id', '=', 'teacher_class_subject.class_id')
            ->where('teacher_class_subject.subject_id', '=', $subject[0]->id)
            ->where('teacher_class_subject.teacher_id', '=', $teacher->id)
            ->distinct()
            ->get(['class.*']);
        if ($class->count() == 0) {
            return ['data' => [], 'status' => '210'];
        }
        $cID = $class[0]->id;
        $class = Classe::findOrFail($cID);
        $data = collect();
        $data->push([
            'class_id' => $cID,
            'subject_name' => $subject[0]->name,
            'lesson_name' => $lesson->title,
            'lesson_number' => $lesson->number,
            'homeworks' => $lesson->homeworks()->get()

        ]);
        return ['data' => $data, 'status' => '210'];
        //send homework for all students in this class
        //send advertisment on class level with assignment info

    }
}
