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
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('file_path')->unique();
            $table->enum('status',['avilable','unavilable'])->default('unavilable');
            $table->enum('term',['s1','s2']);
            $table->enum('type',['first','second','final']);
            $table->timestamp('publish_date');
            $table->foreignId('subject_id')->constrained('subject')->cascadeOnUpdate();
            $table->foreignId('teacher_id')->constrained('employee')->cascadeOnUpdate();
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
        Schema::dropIfExists('exams');
    }
};
