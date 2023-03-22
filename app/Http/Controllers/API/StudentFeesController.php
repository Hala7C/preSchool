<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentFees;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use function PHPUnit\Framework\isEmpty;

class StudentFeesController extends Controller
{

    public function index($id)
    {
        $payments = Student::find($id)->with('fees');
        if ($payments . isEmpty()) {
            return ['data' => 'there is no payments yet', 'status' => '210'];
        }
        return ['data' => $payments, 'status' => '210'];
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => ['required', 'number'],
            'student_id' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $remind = 'calculate the remind for specifiec std';
        $input = [
            'amount' => $request->amount,
            'student_id' => $request->student_id,
            'remaind' => $remind,
        ];
        $payment = StudentFees::create($input);
        return ['data' => $payment, 'status' => '210'];
    }

}
