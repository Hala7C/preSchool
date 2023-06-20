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

use function PHPUnit\Framework\isEmpty;

class StudentFeesController extends Controller
{

    public function index($id)
    {
        $payments = Student::find($id)->with('fees')->get();
        if ($payments ==null) {
            return ['data' => 'there is no payments yet', 'status' => '210'];
        }
        return ['data' => $payments, 'status' => '210'];
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
        $payment = StudentFees::create($input);
        return ['data' => $payment, 'status' => '210'];
    }

    public function unPaidedStudent(){
        $data=collect();
        $students=Student::all();
        foreach($students as $std){
            if($std->withCount('fees')->get()[0]->fees_count==0){
                if($std->currentPayment()->remaind!=0){
                    $data->push([
                        'student'=>$std
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
        ($std->bus()!=null)?$busFees=$year_fees[0]->bus_fees:$busFees=0;
        $std_fees=$year_fees[0]->study_fees+$busFees;
        return $std_fees;
    }


    public function calculateStudentRemind($std_fees,$id,$amount){
        $std=Student::findOrFail($id);
        if($std->withCount('fees')->get()[0]->fees_count==0){
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
        if($std->withCount('fees')->get()[0]->fees_count==0){
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
