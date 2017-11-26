<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->date('arrival');
            $table->date('departure');

            $table->double('basePrice')->default(0.0);
            $table->double('discount')->default(0.0);
            $table->double('deposit')->default(0.0);
            $table->boolean('paid')->default(false);
            $table->boolean('white')->default(true);

            $table->integer('guests')->default(1);
            $table->text('comments')->nullable();

            $table->integer('customer_id')->unsigned();
            $table->foreign('customer_id')->references('id')->on('guests');

            $table->integer('room_id')->unsigned();
            $table->foreign('room_id')->references('id')->on('rooms');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bookings');
    }
}
