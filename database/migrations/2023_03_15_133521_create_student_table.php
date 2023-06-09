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
        Schema::create('student', function (Blueprint $table) {
            $table->id();
            $table->string("fullName");
            $table->enum("gender", ["female", "male"]);
            $table->string("motherName");
            $table->string("motherLastName");
            $table->date("birthday");
            $table->bigInteger("phone");
            $table->string("location");
            $table->integer("siblingNo");
            $table->text("healthInfo");
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
        Schema::dropIfExists('student');
    }
};
