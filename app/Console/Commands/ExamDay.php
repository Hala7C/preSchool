<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Exam;
use Carbon\Carbon;

class ExamDay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exam:corn';

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
        $cuurent=Carbon::now()->setTimezone("GMT+3")->format("Y-m-d");
        $exams=Exam::whereDate('publish_date', $cuurent)->where('status','=','unavilable')->get();
        //fech all exam that published time not done yet
        //for  each exam compare that if its time equals today then change its status to available
        foreach($exams as $exam){
            $exam->status='avilable';
            $exam->save();
        }
    }
}
