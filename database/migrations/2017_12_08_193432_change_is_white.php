<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Booking;

class ChangeIsWhite extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->renameColumn('white', 'ext_booking');
            // $table->boolean('ext_booking')->default(false)->change();
        });

        $bookings = Booking::all();
        foreach ($bookings as $booking) {
            $booking->ext_booking = !$booking->ext_booking;
            $booking->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->renameColumn('ext_booking', 'white');
            // $table->boolean('white')->default(true)->change();
        });

        $bookings = Booking::all();
        foreach ($bookings as $booking) {
            $booking->white = !$booking->white;
            $booking->save();
        }
    }
}
