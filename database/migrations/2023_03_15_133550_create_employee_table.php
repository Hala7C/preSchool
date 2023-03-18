<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee', function (Blueprint $table) {
            $table->id();
            $table->string("fullName");
            $table->enum("gender", ["female", "male"]);
            $table->date("birthday");
            $table->bigInteger("phone");
            $table->string("location");
            $table->text("healthInfo")->nullable();
            $table->enum("degree", ["bachalor", "bachalors", "master"]);
            $table->string("specialization")->nullable();
            $table->timestamps();
        });
        DB::table('employee')->insert(
            array(
                'fullName'     => 'admin',
                'gender' => 'male',
                'birthday'    => '2012-11-12',
                'phone' => '0959906205',
                'location' => 'unkown',
                'degree' => 'bachalor'
            )
        );
        DB::table('users')->insert(
            array(
                'name'     => 'admin',
                'password' => Hash::make('1234567890'),
                'role'    => 'admin',
                'status' => 'active',
                'ownerable_id' =>1,
                'ownerable_type' => 'employee'
            )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee');
    }
};
