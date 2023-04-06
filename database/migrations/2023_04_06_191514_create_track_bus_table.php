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
        Schema::create('track_bus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bus_id')->constrained('bus')->cascadeOnDelete();
            //You can add anthor Foreign ID For the delivery company/person
            $table->double('lng')->nullable();
            $table->double('lat')->nullable();
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
        Schema::dropIfExists('track_bus');
    }
};
