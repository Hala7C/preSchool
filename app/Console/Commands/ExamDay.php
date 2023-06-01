<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Exam;
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
        //fech all exam that published time not done yet
        $exams=Exam::where();
        //for  each exam compare that if its time equals today then change its status to available

    }
}
