<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->integer("age");
            $table->date("birthday");
            $table->bigInteger("phone");
            $table->json("location");
            $table->text("helthInfo");
            $table->enum("degree", ["bachalor", "bachalors", "master"]);
            $table->string("specialization");
            $table->timestamps();
        });
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
