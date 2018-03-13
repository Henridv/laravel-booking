<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExtrasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extras', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->string('name');
            $table->decimal('price', 8, 2);
            $table->string('per');
        });

        Schema::create('booking_extra', function (Blueprint $table) {
            $table->integer('booking_id')->unsigned();
            $table->integer('extra_id')->unsigned();

            $table->foreign('booking_id')
                ->references('id')
                ->on('bookings')
                ->onDelete('cascade');

            $table->foreign('extra_id')
                ->references('id')
                ->on('extras')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('booking_extra');
        Schema::dropIfExists('extras');
    }
}
