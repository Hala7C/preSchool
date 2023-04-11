<?php

namespace Database\Seeders;

use App\Http\Middleware\User;
use App\Models\Student;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Student::factory()->has(\App\Models\User::factory(), 'owner')->count(60)->create();
    }
}
