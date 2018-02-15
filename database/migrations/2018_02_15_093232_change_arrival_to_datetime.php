<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Booking;

class ChangeArrivalToDatetime extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->datetime('arrival_new')
                ->nullable()
                ->default(null)
                ->after('arrival');
        });

        $bookings = Booking::all();
        foreach($bookings as $booking) {
            $booking->timestamps = false;
            $booking->arrival_new = $booking->arrival;
            $booking->arrival_new->hour = 12;
            $booking->save();
        }

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('arrival');
        });
        Schema::table('bookings', function (Blueprint $table) {
            $table->renameColumn('arrival_new', 'arrival');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->renameColumn('arrival', 'arrival_old');
        });
        Schema::table('bookings', function (Blueprint $table) {
            $table->date('arrival')
                ->nullable()
                ->default(null)
                ->after('arrival_old');
        });

        $bookings = Booking::all();
        foreach($bookings as $booking) {
            $booking->timestamps = false;
            $booking->arrival = $booking->arrival_old;
            $booking->save();
        }

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('arrival_old');
        });
    }
}
