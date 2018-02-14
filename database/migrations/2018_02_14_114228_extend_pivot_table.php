<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Booking;

class ExtendPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_room', function (Blueprint $table) {
            $table->json('options')->after('bed')->nullable()->default(NULL);
        });

        $bookings = Booking::all();
        foreach ($bookings as $booking) {
            $props = $booking->rooms[0]->properties;

            $options['part'] = -1;
            $options['asWhole'] = false;
            $props->options = $options;
            $props->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('booking_room', function (Blueprint $table) {
            $table->dropColumn('options');
        });
    }
}
