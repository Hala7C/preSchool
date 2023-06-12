<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AbsenceController extends Controller
{
    //
    public function index(Request $request)
    {
        $absences_students = Absence::filters($request->query())->get();

        return $absences_students;
    }
    public function registerAbsence(Request $request)
    {
        $students = $request->post("students");
        $absences_students_ids = Absence::where('date', Carbon::today())
            ->pluck('student_id')
            ->toArray();
        foreach ($students as $student_id => $justification) {
            if (!in_array($student_id, $absences_students_ids)) {
                Absence::create([
                    "date" => Carbon::today(),
                    "student_id" => $student_id,
                    "justification" => $justification,
                ]);
            }
        }
        return ["absence done"];
    }
    public function updateJustification(Request $request, $id)
    {
        //  $absence = Absence::findOrFail($id);
        $validator = Validator::make($request->all(), [
            "justification" => "required|string|max:255"
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        //Absence::where("id", $id)->update((["justification", $request->justification]));
        DB::table('absences')->where('id', $id)->update(['justification' => $request->justification]);
        return ['data' => ["Absence fixed"], 'status' => '210'];
    }
    public function deleteStudentFromAbsence(Request $request)
    {
        $students = $request->students;
        foreach ($students as $student) {
            $student = Absence::where('date', Carbon::today())
                ->where('student_id', $student)->delete();
        }
        return ['data' => ["Absence fixed"], 'status' => '210'];
    }
}
