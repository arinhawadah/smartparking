<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReservationBuffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservation_buffers', function (Blueprint $table) {
            $table->increments('id_reservation');
            $table->integer('id_user')->unsigned();
            $table->integer('id_slot')->unsigned();
            $table->datetime('validity_limit');

            $table->foreign('id_user')->references('id')->on('users');
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
        Schema::dropIfExists('reservation_buffers');
    }
}
