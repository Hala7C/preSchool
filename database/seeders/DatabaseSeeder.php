<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\{
    Bus,
    Classe,
    User,
    Student,
    Employee,
    Homework,
    Lesson,
    Level,
    StudentClass,
    Subject,
    TeacherClassSubject
};
use Database\Factories\ClassFactory;

class DatabaseSeeder extends Seeder
{

    public function run()
    {
/////////std
        DB::table('student')->insert(
            array(
                'fullName' => "hala",
                'gender' => 'female',
                'motherName' => "dyala",
                'motherLastName' => "kasem",
                'birthday' => '2000-12-11',
                'phone' => '0988738552',
                'location' => "meneen",
                'siblingNo' => "3",
                'healthInfo' => "nuts alergy",
                'bus_id' => null,
                'bus_registry' => true,
                'lng' => '36.345674',
                'lat' => '36.865432',
            )
        );
        DB::table('users')->insert(
            array(
                'name'     => 'user',
                'password' => Hash::make('1234567890'),
                'role'    => 'user',
                'status' => 'active',
                'ownerable_id' => 1,
                'ownerable_type' => 'student'
            )
        );


        /////emp
        DB::table('employee')->insert(
            array(
                'fullName'     => 'rami',
                'gender' => 'male',
                'birthday'    => '2012-11-12',
                'phone' => '0959906205',
                'location' => 'unkown',
                'degree' => 'bachalor'
            )
        );
        DB::table('users')->insert(
            array(
                'name'     => 'employee',
                'password' => Hash::make('1234567890'),
                'role'    => 'employee',
                'status' => 'active',
                'ownerable_id' => 2,
                'ownerable_type' => 'employee'
            )
        );


        //////teacher
        DB::table('employee')->insert(
            array(
                'fullName'     => 'sami',
                'gender' => 'male',
                'birthday'    => '2012-11-12',
                'phone' => '0959906205',
                'location' => 'unkown',
                'degree' => 'bachalor'
            )
        );
        DB::table('users')->insert(
            array(
                'name'     => 'teacher',
                'password' => Hash::make('1234567890'),
                'role'    => 'teacher',
                'status' => 'active',
                'ownerable_id' => 3,
                'ownerable_type' => 'employee
                '
            )
        );

        Employee::factory()->has(User::factory()->state(['role' => 'bus_supervisor']), 'owner')
            ->has(Bus::factory(), 'bus')->count(6)->create();


        ///create 6 std in same region
        Student::factory()->state(['lng' => 36.318054,'lat'=>33.490909,'bus_id'=>1])->has(User::factory(), 'owner')->create();
        Student::factory()->state(['lng' => 36.314659,'lat'=>33.491373,'bus_id'=>1])->has(User::factory(), 'owner')->create();
        Student::factory()->state(['lng' => 36.315196,'lat'=>33.494584,'bus_id'=>1])->has(User::factory(), 'owner')->create();
        Student::factory()->state(['lng' => 36.308919,'lat'=>33.492954,'bus_id'=>1])->has(User::factory(), 'owner')->create();
        Student::factory()->state(['lng' => 36.306030,'lat'=>33.489854,'bus_id'=>1])->has(User::factory(), 'owner')->create();
        Student::factory()->state(['lng' => 36.304149,'lat'=>33.492808,'bus_id'=>1])->has(User::factory(), 'owner')->create();
        Student::factory()->state(['lng' => 36.310897,'lat'=>33.495811,'bus_id'=>1])->has(User::factory(), 'owner')->create();
//////////////////////
        Student::factory()->has(User::factory(), 'owner')->count(53)->create();

        // // $levels=Level::factory()->count(2)->create();
        // // $classes=Classe::factory()->count(4)->for($levels)->create();
        // // $subjects=Subject::factory()->count(7)->for($levels)->create();
        // // $lessons=Lesson::factory()->count(12)->for($subjects)->hasAttached($classes,['status'=>'ungiven'])->create();
        // // $homeworks=Homework::factory()->count(2)->for($lessons)->create();

        Level::factory()->has(Classe::factory()->count(4),'classes')
                            ->has(Subject::factory()->has(
                                Lesson::factory()
                                ->has(
                                    Homework::factory()->count(2),'homeworks')->count(12),'lessons')->count(7)
                                        ,'subjects')->count(2)->create();





            $classes=Classe::all();
            $students=Student::all();
            $count=0;
            foreach($classes as $class){
                for($i=0;$i<$class->capacity;$i++){
                    if($count < count($students)){
                    StudentClass::create([
                        'student_id'=>$students[$count]->id,
                        'class_id'=>$class->id
                    ]);
                    $count++;
                }}
            }
/////////////////teachers
            Employee::factory()->has(User::factory()->state(['role' => 'teacher']), 'owner')->count(7)->create();
            //TeacherClassSubject
            $classes=Classe::all();
            $users=User::all()->where('role','=','teacher');
            $teachers =collect();
            foreach ($users as $u){
                $user=$u->ownerable;
                $teachers->push([
                    'id'=>$user->id,
                ]);
            }
            $subjects=Subject::all();
            $count=0;
            foreach($classes as $class){
                foreach($subjects as  $subject){
                    if($class->level_id == $subject->level_id ){
                        if($count < count($teachers)){
                        TeacherClassSubject::create([
                        'teacher_id'=>$teachers[$count]["id"],
                        'class_id'=>$class->id,
                        'subject_id'=>$subject->id
                    ]);

                }}}$count++;
            }


    }
}
