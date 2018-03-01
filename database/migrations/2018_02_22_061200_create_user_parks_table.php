<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserParksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_parks', function (Blueprint $table) {
            $table->increments('id_user_park');
            $table->integer('id_user')->unsigned();
            $table->integer('id_slot')->unsigned();
            $table->string('unique_id');
            $table->datetime('arrive_time');
            $table->datetime('leaving_time');
            $table->integer('id_reservation')->unsigned();

            $table->foreign('id_user')->references('id')->on('users');
            $table->foreign('id_slot')->references('id_slot')->on('car_park_slots');
            $table->foreign('id_reservation')->references('id_reservation')->on('reservation_buffers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_parks');
    }
}
