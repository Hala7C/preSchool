<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->time('start_time');
            $table->double('lng')->nullable();
            $table->double('lat')->nullable();
            $table->bigInteger('phone');
            $table->timestamps();
        });

        DB::table('schools')->insert(
            array(
                'name' => 'Jaaber Al-ansari',
                'start_time'=>'07:00:00',
                'lng' => null,
                'lat' =>null ,
                'phone' => '0933130997'
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
        Schema::dropIfExists('schools');
    }
};
