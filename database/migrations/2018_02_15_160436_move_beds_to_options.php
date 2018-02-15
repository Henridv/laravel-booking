<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Booking;

class MoveBedsToOptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $bookings = Booking::all();
        foreach($bookings as $booking) {
            $beds = [];
            $room_ids = [];
            $i=0;

            if (!$booking->rooms->isEmpty()) {
                foreach($booking->rooms as $booked_room) {
                    $beds[] = $booked_room->properties->bed;
                    $room_ids[] = $booked_room->id;
                    if ($i++ > 0) {
                        $booking->rooms()->detach($booked_room->id);
                    }
                }

                $room_id = $room_ids[0];
                $options = $booking->rooms[0]->properties->options;

                $options['beds'] = $beds;
                $booking->rooms()->sync([$room_id => ['bed' => 1, 'options' => $options]]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $bookings = Booking::all();
        foreach($bookings as $booking) {
            $room_ids = [];
            $i=0;

            if (!$booking->rooms->isEmpty()) {
                foreach($booking->rooms as $booked_room) {
                    $beds = $booked_room->properties->options['beds'];
                    $options = $booked_room->properties->options;
                    unset($options['beds']);

                    $booking->rooms()->detach($booked_room->id);
                    foreach($beds as $bed) {
                        $booking->rooms()->save($booked_room, ['bed' => $bed, 'options' => $options]);
                    }
                }
            }
        }
    }
}
