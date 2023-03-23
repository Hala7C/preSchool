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
        Schema::create('year_config', function (Blueprint $table) {
            $table->id();
            $table->year('year');
            $table->bigInteger("study_fees");
            $table->bigInteger("bus_fees");
            $table->decimal("discount_bus");
            $table->decimal("discount_without_bus");
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
        Schema::dropIfExists('year_config');
    }
};
