<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Student;
use Carbon\Carbon;
use App\Models\StudentFees;
use App\Models\YearConfig;
use App\Models\FeesConfig;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use function PHPUnit\Framework\isNull;

class StudentFeesController extends Controller
{

    public function index($id)
    {
        $student = Student::find($id);
        $payments = $student->withCount('fees')->where('id', $id)->get();
        if ($payments[0]->fees_count == 0) {
            return ['data' => [], 'status' => '210'];
        }
        $data = $student->fees()->get();
        $res = collect();
        foreach ($data as $d) {
            $emp = Employee::find($d->employee_id);
            $res->push([
                'id' =>    $d->id,
                'amount' =>    $d->amount,
                'remaind'    => $d->remaind,
                'employee_id' => $d->employee_id,
                'employee_name' => $emp->fullName
            ]);
        }
        return ['data' => $res, 'status' => '210'];
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
        $std_fees = $this->getStudentFees($request->student_id);
        $remind = $this->calculateStudentRemind($std_fees, $request->student_id, $request->amount);
        $user = Auth::user();
        $emp = $user->ownerable;
        $input = [
            'amount' => $request->amount,
            'student_id' => $request->student_id,
            'remaind' => $remind,
            'employee_id' => $emp->id
        ];
        if ($remind < 0) {
            return response()->json(['data' => 'the entered amount is greater than remaining amount !!', 'status' => 400], 400);
        }
        $payment = StudentFees::create($input);
        return ['data' => $payment, 'status' => '210'];
    }

    public function unPaidedStudent()
    {
        $data = collect();
        $cuurentYear = Carbon::now()->year;
        $year_fees = YearConfig::where('year', $cuurentYear)->get();
        $students = Student::all();
        foreach ($students as $std) {
            $payments = $std->withCount('fees')->where('id', $std->id)->get();
            if ($payments[0]->fees_count == 0) {
                $std_fees = (new StudentFeesController)->getStudentFees($std->id);
                $remind = (new StudentFeesController)->getStudentRemind($std_fees, $std->id);
                $cPaid = $std_fees - $remind;
                $status = (new StudentFeesController)->getStatus($std->id);
                // $payments =$std->withCount('fees')->where('id',$std->id)->get();
                ($std->bus_registry == true) ? $busFees = $year_fees[0]->bus_fees : $busFees = 0;
                ($std->study_discount == true) ? $discountFees = $year_fees[0]->discount_without_bus : $discountFees = 0;
                ($std->bus_discount == true) ? $discountbusFees = $year_fees[0]->discount_bus : $discountbusFees = 0;
                $data->push([
                    'id' => $std->id,
                    'name' => $std->fullName,
                    'fees' => $std_fees,
                    'current_amount' => $cPaid,
                    'remind' => $remind,
                    'bus_fees' => $busFees,
                    'study_discount' => $discountFees,
                    'bus_discount' => $discountbusFees,
                    'status' => $status,

                ]);
            }
        }
        return ['data' => $data, 'status' => '210'];
    }
    public function PaidedStudent()
    {
        $data = collect();
        $cuurentYear = Carbon::now()->year;
        $year_fees = YearConfig::where('year', $cuurentYear)->get();
        $students = Student::all();
        foreach ($students as $std) {
            $payments = $std->withCount('fees')->where('id', $std->id)->get();
            if ($payments[0]->fees_count > 0) {
                $std_fees = (new StudentFeesController)->getStudentFees($std->id);
                $remind = (new StudentFeesController)->getStudentRemind($std_fees, $std->id);
                $cPaid = $std_fees - $remind;
                $status = (new StudentFeesController)->getStatus($std->id);
                // $payments =$std->withCount('fees')->where('id',$std->id)->get();
                ($std->bus_registry == true) ? $busFees = $year_fees[0]->bus_fees : $busFees = 0;
                ($std->study_discount == true) ? $discountFees = $year_fees[0]->discount_without_bus : $discountFees = 0;
                ($std->bus_discount == true) ? $discountbusFees = $year_fees[0]->discount_bus : $discountbusFees = 0;
                $data->push([
                    'id' => $std->id,
                    'name' => $std->fullName,
                    'fees' => $std_fees,
                    'current_amount' => $cPaid,
                    'remind' => $remind,
                    'bus_fees' => $busFees,
                    'study_discount' => $discountFees,
                    'bus_discount' => $discountbusFees,
                    'status' => $status,
                ]);
            }
        }
        return ['data' => $data, 'status' => '210'];
    }
    public function CompletePaidedStudent()
    {
        $data = collect();
        $cuurentYear = Carbon::now()->year;
        $year_fees = YearConfig::where('year', $cuurentYear)->get();
        $students = Student::all();
        foreach ($students as $std) {
            $payments = $std->withCount('fees')->where('id', $std->id)->get();
            if ($payments[0]->fees_count > 0) {
                if ($std->currentPayment()->remaind == 0) {
                    $std_fees = (new StudentFeesController)->getStudentFees($std->id);
                    $remind = (new StudentFeesController)->getStudentRemind($std_fees, $std->id);
                    $cPaid = $std_fees - $remind;
                    $status = (new StudentFeesController)->getStatus($std->id);
                    // $payments =$std->withCount('fees')->where('id',$std->id)->get();
                    ($std->bus_registry == true) ? $busFees = $year_fees[0]->bus_fees : $busFees = 0;
                    ($std->study_discount == true) ? $discountFees = $year_fees[0]->discount_without_bus : $discountFees = 0;
                    ($std->bus_discount == true) ? $discountbusFees = $year_fees[0]->discount_bus : $discountbusFees = 0;
                    $data->push([
                        'id' => $std->id,
                        'name' => $std->fullName,
                        'fees' => $std_fees,
                        'current_amount' => $cPaid,
                        'remind' => $remind,
                        'bus_fees' => $busFees,
                        'study_discount' => $discountFees,
                        'bus_discount' => $discountbusFees,
                        'status' => $status,
                    ]);
                }
            }
        }
        return ['data' => $data, 'status' => '210'];
    }
    public function allStudentInfo()
    {
        $data = collect();
        $students = Student::all();
        $cuurentYear = Carbon::now()->year;
        $year_fees = YearConfig::where('year', $cuurentYear)->get();
        foreach ($students as $std) {
            $std_fees = (new StudentFeesController)->getStudentFees($std->id);
            $remind = (new StudentFeesController)->getStudentRemind($std_fees, $std->id);
            $cPaid = $std_fees - $remind;
            $status = (new StudentFeesController)->getStatus($std->id);
            // $payments =$std->withCount('fees')->where('id',$std->id)->get();
            ($std->bus_registry == true) ? $busFees = $year_fees[0]->bus_fees : $busFees = 0;
            ($std->study_discount == true) ? $discountFees = $year_fees[0]->discount_without_bus : $discountFees = 0;
            ($std->bus_discount == true) ? $discountbusFees = $year_fees[0]->discount_bus : $discountbusFees = 0;
            $data->push([
                'id' => $std->id,
                'name' => $std->fullName,
                'fees' => $std_fees,
                'current_amount' => $cPaid,
                'remind' => $remind,
                'bus_fees' => $busFees,
                'study_discount' => $discountFees,
                'bus_discount' => $discountbusFees,
                'status' => $status,
            ]);
        }
        return ['data' => $data, 'status' => '210'];
    }

    public function getStatus($id)
    {
        $std = Student::findOrFail($id);
        $payments = $std->withCount('fees')->where('id', $id)->get();
        if ($payments[0]->fees_count == 0) {
            $remind = 'unPaid';
        } elseif ($std->currentPayment()->remaind == 0) {
            $remind = 'complete';
        } else {
            $remind = 'Paid';
        }
        return $remind;
    }
    public function getStudentFees($id)
    {
        $cuurentYear = Carbon::now()->year;
        $year_fees = YearConfig::where('year', $cuurentYear)->get();
        $std = Student::findOrFail($id);
        ($std->bus_registry == true) ? $busFees = $year_fees[0]->bus_fees : $busFees = 0;
        ($std->study_discount == true) ? $discountFees = $year_fees[0]->discount_without_bus : $discountFees = 0;
        ($std->bus_discount == true) ? $discountbusFees = $year_fees[0]->discount_bus : $discountbusFees = 0;
        $std_fees = $year_fees[0]->study_fees + $busFees + $discountFees + $discountbusFees;
        return $std_fees;
    }


    public function calculateStudentRemind($std_fees, $id, $amount)
    {
        $std = Student::findOrFail($id);
        $payments = $std->withCount('fees')->where('id', $id)->get();
        if ($payments[0]->fees_count == 0) {

            $remind = $std_fees - $amount;
        } elseif ($std->currentPayment()->remaind == 0) {
            $remind = 'the fees are completed';
        } else {
            $remind = $std->currentPayment()->remaind - $amount;
        }
        return $remind;
    }


    public function getStudentRemind($std_fees, $id)
    {
        $std = Student::findOrFail($id);
        $payments = $std->withCount('fees')->where('id', $id)->get();
        if ($payments[0]->fees_count == 0) {
            $remind = $std_fees;
        } elseif ($std->currentPayment()->remaind == 0) {
            $remind = 0;
        } else {
            $remind = $std->currentPayment()->remaind;
        }
        return $remind;
    }

    // public function sendNotification(){
    //     $data=collect();
    //     $wantedDates=FeesConfig::all();
    //     $currentDate= Carbon::createFromFormat('m/d/Y', Carbon::now());
    //     $students=Student::all();
    //     foreach($wantedDates as $wdate ){
    //         $wantedDay = Carbon::createFromFormat('m/d/Y',$wdate->date);
    //         if($currentDate->eq($wantedDay)){
    //             $persent=$wdate->amount;
    //             foreach($students as $std){
    //                 $std_fees=(new StudentFeesController)->getStudentFees($std->id);
    //                 $remind=(new StudentFeesController)->getStudentRemind($std_fees,$std->id);
    //                 $cPaid=$std_fees -$remind;
    //                 // $std_persernt=($cPaid *100)/ $std_fees;
    //                 if($cPaid<$persent){
    //                     /*send notification*/
    //                     $current_remaining_payment=$persent-$cPaid;
    //                     $data->push([
    //                         'id'=>$std->id,
    //                         'name'=>$std->fullName,
    //                         'current_amount'=>$cPaid,
    //                         'current_remaining_payment'=>$current_remaining_payment,
    //                         'total_remaining_payment'=>$remind,
    //                         'type'=>'late paid'
    //                     ]);
    //                 }
    //             }
    //         }
    //     }
    //     return ['data'=>$data,'status'=>210];
    // }

    public function getAllLateStudentNotifications()
    {
        $notifications = Notification::all()->where('type', '=', 'late paid');
        $data = collect();
        $wantedDates = FeesConfig::all();
        $students = Student::all();
        foreach ($wantedDates as $wdate) {
            $stds = collect();
            foreach ($notifications as $notification) {
                if ($wdate->id == $notification->config_id) {
                    $student = $notification->student()->get();
                    $std = Student::find($student[0]->id);
                    $std_fees = (new StudentFeesController)->getStudentFees($std->id);
                    $remind = (new StudentFeesController)->getStudentRemind($std_fees, $std->id);
                    $cPaid = $std_fees - $remind;
                    $current_remaining_payment = $notification->current_remaining_payment;
                    $stds->push([
                        'id' => $std->id,
                        'name' => $std->fullName,
                        'current_amount' => $cPaid,
                        'current_remaining_payment' => $current_remaining_payment,
                        'total_remaining_payment' => $remind,
                    ]);
                }
            }
            if ($stds->count() > 0) {
                $data->push([
                    'date' => $wdate->date,
                    'amount' => $wdate->amount,
                    'notification_id' => $wdate->id,
                    'students_info' => $stds,
                ]);
            }
        }

        return ['data' => $data, 'type' => 'late paid', 'status' => 210];
    }


    public function removeNotification($id)
    {
        $notifications = Notification::where('config_id', '=', $id)->get();
        foreach ($notifications as $notifi) {
            $notifi->destroy($notifi->id);
        }
        return ['message' => 'notification deleted successfly'];
    }

    public function latePaymentStudents()
    {
        $data = collect();
        $cuurentYear = Carbon::now()->year;
        $year_fees = YearConfig::where('year', $cuurentYear)->get();
        $cd = Carbon::now()->setTimezone("GMT+3")->format("Y-m-d");
        $wantedDate = FeesConfig::whereDate('date', '<', $cd)->orderBy('date', 'desc')->first();
        $students = Student::all();
        $persent = $wantedDate->amount;
        foreach ($students as $std) {

            ($std->bus_registry == true) ? $busFees = $year_fees[0]->bus_fees : $busFees = 0;
            ($std->study_discount == true) ? $discountFees = $year_fees[0]->discount_without_bus : $discountFees = 0;
            ($std->bus_discount == true) ? $discountbusFees = $year_fees[0]->discount_bus : $discountbusFees = 0;
            $status = (new StudentFeesController)->getStatus($std->id);
            $std_fees = (new StudentFeesController)->getStudentFees($std->id);
            $remind = (new StudentFeesController)->getStudentRemind($std_fees, $std->id);
            $cPaid = $std_fees - $remind;
            if ($cPaid < $persent) {
                $should_paid = $persent - $cPaid;
                $data->push([
                    'id' => $std->id,
                    'name' => $std->fullName,
                    'current_amount' => $cPaid,
                    'remind' => $remind,
                    'fees' => $std_fees,
                    'bus_fees' => $busFees,
                    'study_discount' => $discountFees,
                    'bus_discount' => $discountbusFees,
                    'status' => $status,
                    'assumed_amount' => $should_paid,

                ]);
            }
        }
        return ['data' => $data, 'status' => 210];
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
