<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Absence;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Throwable;

class AbsenceController extends Controller
{
    //
    public function index(Request $request)
    {
        $absences_students = Absence::filters($request->query())->get();
        $data = collect();
        foreach ($absences_students as $student_info) {
            $std = Student::findOrFail($student_info->student_id);
            $data->push([
                'id' => $std->id,
                'date' => $student_info->date,
                'justification' => $student_info->justification,
                'fullName' => $std->fullName,
                'phone' => $std->phone
            ]);
        }

        return ['data' => $data, 'status' => 210];
    }



    public function registerjson(Request $request)
    {
        $encodded_data = $request->json()->all();
        $decodded_data = json_encode($encodded_data);
        $data = json_decode($decodded_data);
        $absences_students_ids = Absence::where('date', Carbon::today())->pluck('student_id')->toArray();
        foreach ($data as $students) {
            $absences = collect();
            foreach ($students as $student) {
                foreach ($student as $key => $Value) {
                    //  DB::beginTransaction();
                    if ($key == "id") {
                        $id = $Value;
                        if (!in_array($id, $absences_students_ids)) {
                            Absence::create([
                                "date" => Carbon::today(),
                                "student_id" => $id,
                                "justification" => null,
                            ]);
                        }
                    }
                }
            }
        }
        return ['data' => ["Absence done"], 'status' => 210];
        // DB::commit();
        //  return $absences;
        //return ['data' => ["Absence dont"], 'status' => 401];
    }
    // public function registerAbsence(Request $request)
    // {
    //     $students = $request->post("students");
    //     $absences_students_ids = Absence::where('date', Carbon::today())
    //         ->pluck('student_id')
    //         ->toArray();
    //     foreach ($students as $student_id => $justification) {
    //         if (!in_array($student_id, $absences_students_ids)) {
    //             Absence::create([
    //                 "date" => Carbon::today(),
    //                 "student_id" => $student_id,
    //                 "justification" => $justification,
    //             ]);
    //         }
    //     }
    //     return ["absence done"];
    // }
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
        DB::table('absences')->where('student_id', $id)->update(['justification' => $request->justification]);
        return ['data' => ["Absence fixed"], 'status' => '210'];
    }
    public function deleteStudentFromAbsence($id)
    {
        $absence = Absence::where('student_id', $id)->where('date', Carbon::today()->format('Y/m/d'))->first();
        if ($absence) {
            //Absence::destroy($id);
            $absence->delete();
            return ['data' => ["Absence fixed"], 'status' => '210'];
        }
        return ["data" => ["Sorry you can't delete "], 'status' => '401'];
    }
}
