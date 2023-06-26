<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Carbon\Carbon;
use App\Models\StudentFees;
use App\Models\YearConfig;
use App\Models\FeesConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class StudentFeesController extends Controller
{

    public function index($id)
    {
        $student=Student::find($id);
        $payments =$student->withCount('fees')->where('id',$id)->get();
        if ($payments[0]->fees_count==0) {
            return ['data' => [], 'status' => '210'];
        }
        $data=$student->fees()->get();
        return ['data' => $data, 'status' => '210'];
    }


    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'amount' => ['required', 'numeric'],
            'student_id' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $std_fees=$this->getStudentFees($request->student_id);
        $remind=$this->calculateStudentRemind($std_fees,$request->student_id,$request->amount);
        $input = [
            'amount' => $request->amount,
            'student_id' => $request->student_id,
            'remaind' => $remind,
        ];
        if($remind < 0 ){
            return response()->json(['data'=>'the entered amount is greater than remaining amount !!', 'status'=>400], 400);
        }
        $payment = StudentFees::create($input);
        return ['data' => $payment, 'status' => '210'];
    }

    public function unPaidedStudent(){
        $data=collect();
        $students=Student::all();
        foreach($students as $std){
            $payments =$std->withCount('fees')->where('id',$std->id)->get();
            if ($payments[0]->fees_count==0) {
                    $data->push([
                        'id'=>$std->id,
                        'name'=>$std->fullName
                    ]);
                }
            }
        return ['data' => $data, 'status' => '210'];
    }
    public function PaidedStudent(){
        $data=collect();
        $students=Student::all();
        foreach($students as $std){
            $payments =$std->withCount('fees')->where('id',$std->id)->get();
            if ($payments[0]->fees_count>0) {
                    $data->push([
                        'id'=>$std->id,
                        'name'=>$std->fullName
                    ]);
                }
            }
        return ['data' => $data, 'status' => '210'];
    }
    public function CompletePaidedStudent(){
        $data=collect();
        $students=Student::all();
        foreach($students as $std){
            $payments =$std->withCount('fees')->where('id',$std->id)->get();
            if ($payments[0]->fees_count>0) {
                if($std->currentPayment()->remaind==0){
                    $data->push([
                        'id'=>$std->id,
                        'name'=>$std->fullName
                    ]);
                }
                }
            }
        return ['data' => $data, 'status' => '210'];
    }
    public function getStudentFees($id){
        $cuurentYear=Carbon::now()->year;
        $year_fees=YearConfig::where('year',$cuurentYear)->get();
        $std=Student::findOrFail($id);
        ($std->bus_registry==true)?$busFees=$year_fees[0]->bus_fees:$busFees=0;
        $std_fees=$year_fees[0]->study_fees+$busFees;
        return $std_fees;
    }


    public function calculateStudentRemind($std_fees,$id,$amount){
        $std=Student::findOrFail($id);
        $payments =$std->withCount('fees')->where('id',$id)->get();
        if ($payments[0]->fees_count==0) {

            $remind=$std_fees-$amount;
        }elseif($std->currentPayment()->remaind==0){
            $remind='the fees are completed';
        }
        else{
            $remind = $std->currentPayment()->remaind - $amount;
        }
        return $remind;
    }


    public function getStudentRemind($std_fees,$id){
        $std=Student::findOrFail($id);
        $payments =$std->withCount('fees')->where('id',$id)->get();
        if ($payments[0]->fees_count==0) {
            $remind=$std_fees;
        }elseif($std->currentPayment()->remaind==0){
            $remind=0;
        }
        else{
            $remind = $std->currentPayment()->remaind ;
        }
        return $remind;
    }

    public function sendNotification(){
        $data=collect();
        $wantedDates=FeesConfig::all();
        $currentDate= Carbon::createFromFormat('m/d/Y', Carbon::now());
        $students=Student::all();
        foreach($wantedDates as $wdate ){
            $wantedDay = Carbon::createFromFormat('m/d/Y',$wdate->date);
            if($currentDate->eq($wantedDay)){
                $persent=$wdate->amount;
                foreach($students as $std){
                    $std_fees=(new StudentFeesController)->getStudentFees($std->id);
                    $remind=(new StudentFeesController)->getStudentRemind($std_fees,$std->id);
                    $cPaid=$std_fees -$remind;
                    $std_persernt=($cPaid *100)/ $std_fees;
                    if($std_persernt<$persent){
                        /*send notification*/
                        $data->push([
                            'student_name'=>$std->fullName,
                            'content'=>'pleas pay your fees',
                            'amount'=>$cPaid
                        ]);
                    }
                }
            }
        }
        return ['data'=>$data,'status'=>210];
    }



    public function latePaymentStudents(){
        $data=collect();
        $cd=Carbon::now()->setTimezone("GMT+3")->format("Y-m-d");
        $wantedDate=FeesConfig::whereDate('date','<', $cd)->orderBy('date','desc')->first();
        $students=Student::all();
        $persent=$wantedDate->amount;
        foreach($students as $std){
            $std_fees=(new StudentFeesController)->getStudentFees($std->id);
            $remind=(new StudentFeesController)->getStudentRemind($std_fees,$std->id);
            $cPaid=$std_fees -$remind;
            if($cPaid<$persent){
                $should_paid=$persent-$cPaid;
                $data->push([
                    'id'=>$std->id,
                    'name'=>$std->fullName,
                    'current_amount'=>$cPaid,
                    'assumed_amount'=>$should_paid,
                ]);
            }}
        return ['data'=>$data,'status'=>210];
    }







    //////////////////////////////////////////////////////////////////////////////////
    ///////////////////copy
    // public function getStudentFeesCopy($id){
    //     $cuurentYear=Carbon::now()->year;
    //     $year_fees=YearConfig::where('year',$cuurentYear)->get();
    //     $std=Student::findOrFail($id);
    //     ($std->bus_registry)?$busFees=$year_fees[0]->bus_fees:$busFees=0;
    //     $std_fees=$year_fees[0]->study_fees+$busFees;
    //     return $std_fees;
    // }
}
