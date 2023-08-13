<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Middleware\Employee;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Subject;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class ExamController extends Controller
{

    public function index($id)
    {
        $subject = Subject::findOrFail($id);
        $subjectsExam = $subject->exams()->get();
        if ($subjectsExam->count() == 0) {
            return ['data' => [], 'status' => 210];
        }
        return ['data' => $subjectsExam, 'status' => 210];
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            // 'name'=>['required','unique:exams,name','string'],
            'file' => ['required', 'file', 'mimes:pdf,xlx,csv,docx', 'max:2048', Rule::unique('exams', 'name')],
            'status' => ['required', 'in:avilable,unavilable'],
            'term' => ['required', 'in:s1,s2'],
            'type' => ['required', 'in:first,second,final'],
            'publish_date' => ['required'],
            'subject_id' => ['required', 'exists:subject,id'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $fileName = $request->file->getClientOriginalName();
        $validat = ['name' => $fileName];
        $validator = Validator::make($validat, [
            'name' => [Rule::unique('exams', 'name')]
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $request->file->move(public_path('exams'), $fileName);
        $myDate =  $request->publish_date;
        $date = Carbon::createFromFormat('d/m/Y H:i', $myDate)->format('Y-m-d H:i');
        $exam =  Exam::create([
            'name' => $fileName,
            'file_path' => 'exams/' . $fileName,
            'status' => $request->status,
            'term' => $request->term,
            'type' => $request->type,
            'publish_date' => $date,
            'subject_id' => $request->subject_id,
            'teacher_id' => Auth::user()->ownerable->id
        ]);
        $status = 210;
        return ['data' => $exam, 'status' => $status];
    }

    public function show($id)
    {
        $exam = Exam::findOrFail($id);
        return ['data' => $exam, 'status' => 210];
    }

    public function update(Request $request, $id)
    {

        $exam = Exam::findOrFail($id);

        ////////////can edit if publish date not coming yet
        $publishDay = $exam->publish_date;
        $published = Carbon::parse($publishDay)->format("Y-m-d");
        $cuurent = Carbon::now()->setTimezone("GMT+3")->format("Y-m-d");
        if (Carbon::parse($cuurent)->gt($published)) {
            // return ['data'=>['You can\'t edit this exam anymore becuase the time out'],'status'=>210];
            return ['data' => [], 'status' => 210];
        }

        $validator = Validator::make($request->all(), [
            'file' => ['sometimes', 'mimes:pdf,xlx,csv,docx', 'max:2048', Rule::unique('exams', 'name')->ignore($exam->id)],
            'status' => ['required', 'in:avilable,unavilable'],
            'term' => ['required', 'in:s1,s2'],
            'type' => ['required', 'in:first,second,final'],
            'publish_date' => ['required'],
            'subject_id' => ['required', 'exists:subject,id'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 210);
        }
        if ($request->has('file')) {
            $fileName = $request->file->getClientOriginalName();
            $request->file->move(public_path('exams'), $fileName);
            $exam->name = $fileName;
            $exam->file_path = 'exams/' . $fileName;
        }

        $myDate =  $request->publish_date;
        $date = Carbon::createFromFormat('d/m/Y H:i', $myDate)->format('Y-m-d H:i');
        $exam->status = $request->status;
        $exam->term = $request->term;
        $exam->type = $request->type;
        $exam->publish_date = $date;
        $exam->subject_id = $request->subject_id;
        $exam->teacher_id = Auth::user()->ownerable->id;
        $exam->save();
        $status = 210;
        return ['data' => $exam, 'status' => $status];
    }

    public function destroy($id)
    {
        $exam = Exam::findOrFail($id);
        if (File::exists($exam->file_path)) {
            File::delete(public_path($exam->file_path));
        }
        $exam->destroy($id);
        return ['data' => 'question deleted successfly'];
    }


    ///////////////////////////////////////////////////////////////////////////////////////////////
    /////////employee section
    public function TodayExam()
    {
        //file - subject - class - teacher
        $cuurent = Carbon::now()->setTimezone("GMT+3")->format("Y-m-d");
        $data = collect();
        $exams = Exam::whereDate('publish_date', $cuurent)->where('status', '=', 'avilable')->get();
        if ($exams->count() == 0) {
            return ['data' => [], 'status' => 210];
        }
        foreach ($exams as $exam) {
            $examDate = $exam->publish_date;
            $date = Carbon::parse($examDate)->format('H:i');
            // return $examDate;
            $subName = $exam->subject()->get()[0]->name;
            $subID = $exam->subject()->get()[0]->id;
            $teacherName = $exam->teacher()->get()[0]->fullName;
            $className = $exam->class()->get('name');
            $classID = $exam->class()->get('id');
            $data->push([
                'name' => $exam->name,
                'file_path' => $exam->file_path,
                'status' => $exam->status,
                'term' => $exam->term,
                'type' => $exam->type,
                'publish_date' => $date,
                'subject_name' => $subName,
                'subject_id' => $subID,
                'teacher_id' => $exam->teacher_id,
                'teacher_name' => $teacherName,
                'class_id' => $classID,
                'class_name' => $className
            ]);
        }
        return ['data' => $data, 'status' => 210];
    }
}
