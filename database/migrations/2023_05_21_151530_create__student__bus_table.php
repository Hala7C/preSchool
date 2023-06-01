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
        Schema::create('student_bus', function (Blueprint $table) {
            $table->id();
            $table->double('lng')->nullable();
            $table->double('lat')->nullable();
            $table->time('arrival_time')->nullable();
            $table->foreignId('bus_id')->nullable()->constrained('bus')->cascadeOnDelete();
            $table->foreignId('student_id')->unique()->constrained('student')->cascadeOnUpdate();
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
        Schema::dropIfExists('student_bus');
    }
};
