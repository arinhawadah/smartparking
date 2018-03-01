<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarParkSlotDumpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_park_slot_dumps', function (Blueprint $table) {
            $table->increments('id_dump');
            $table->integer('id_slot')->unsigned();
            $table->string('status');
            $table->float('coordinate');
            $table->timestamps();

            $table->foreign('id_slot')->references('id_slot')->on('car_park_slots');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('car_park_slot_dumps');
    }
}
