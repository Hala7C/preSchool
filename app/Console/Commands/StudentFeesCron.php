<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Student;
use Carbon\Carbon;
use App\Models\StudentFees;
use App\Models\YearConfig;
use App\Models\FeesConfig;
use App\Http\Controllers\API\StudentFeesController;
use App\Models\Notification;

class StudentFeesCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fees:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $wantedDates = FeesConfig::all();
        $cd = Carbon::now()->setTimezone("GMT+3")->format("Y-m-d");
        $currentDate = Carbon::createFromFormat('Y-m-d', $cd);
        $students = Student::all();
        foreach ($wantedDates as $wdate) {
            $wantedDay = Carbon::createFromFormat('Y-m-d', $wdate->date);
            if ($currentDate->eq($wantedDay)) {
                $persent = $wdate->amount;
                foreach ($students as $std) {
                    $std_fees = (new StudentFeesController)->getStudentFees($std->id);
                    $remind = (new StudentFeesController)->getStudentRemind($std_fees, $std->id);
                    $cPaid = $std_fees - $remind;
                    // $std_persernt=($cPaid *100)/ $std_fees;
                    if ($cPaid < $persent) {
                        /*send notification*/
                        $current_remaining_payment = $persent - $cPaid;
                        Notification::create([
                            'student_id' => $std->id,
                            'current_remaining_payment' => $current_remaining_payment,
                            'type' => 'late paid',
                            'config_id' => $wdate->id
                        ]);
                    }
                }
            }
        }
    }
}
