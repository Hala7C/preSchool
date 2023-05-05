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
    Subject,
};
use Database\Factories\ClassFactory;

class DatabaseSeeder extends Seeder
{

    public function run()
    {

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

        Student::factory()->has(User::factory(), 'owner')->count(60)->create();


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


                                        Employee::factory()->has(User::factory()->state(['role' => 'teacher']), 'owner')
                                        ->has(Bus::factory(), 'bus')->count(6)->create();

    }
}
