<?php

namespace App\Console\Commands;

use App\Models\Classe;
use App\Models\Lesson;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;

class LessonStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lesson:status';

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
        $lessons=Lesson::all();
        $classes=Classe::all();
        foreach($lessons as $lesson){
            foreach($classes as $class){
                DB::table('lesson_class')
                ->where('lesson_id',$lesson->id)
                ->where('class_id',$class->id)
                ->limit(1)
                ->update(array('status'=>'ungiven'));
                // $lessonStatus=$lesson->classes()->updateExistingPivot($class->id,['status'=>'ungiven']);
            }
        }
    }
}
