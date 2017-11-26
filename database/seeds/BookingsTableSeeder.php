<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

use App\Booking;
use App\Guest;
use App\Room;

class BookingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Carbon::setWeekStartsAt(Carbon::SATURDAY);

        $booking = new Booking();

        $booking->arrival = Carbon::parse('now')->startOfWeek()->addDays(rand(0,2));
        $booking->departure = Carbon::parse($booking->arrival)->addDays(rand(1,5));

        $booking->guests = rand(1,4);
        $booking->basePrice = rand(5,10)*10;
        $booking->discount = rand(1,10)*10;
        $booking->deposit = (int)$booking->basePrice/(rand(1,2)*5);

        $booking->customer_id = rand(1,Guest::count());
        $booking->room_id = rand(1,Room::count());
        
        $booking->comments = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut consequat, nunc ut lobortis dapibus, tortor sapien tristique leo, sed molestie.";

        $booking->save();
        $booking = new Booking();

        $booking->arrival = Carbon::parse('now')->startOfWeek()->addDays(rand(0,2));
        $booking->departure = Carbon::parse($booking->arrival)->addDays(rand(1,5));

        $booking->guests = rand(1,4);
        $booking->basePrice = rand(5,10)*10;
        $booking->discount = rand(1,10)*10;
        $booking->deposit = (int)$booking->basePrice/(rand(1,2)*5);

        $booking->customer_id = rand(1,Guest::count());
        $booking->room_id = rand(1,Room::count());

        $booking->comments = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut consequat, nunc ut lobortis dapibus, tortor sapien tristique leo, sed molestie.";

        $booking->save();
        $booking = new Booking();

        $booking->arrival = Carbon::parse('now')->startOfWeek()->addDays(rand(0,2));
        $booking->departure = Carbon::parse($booking->arrival)->addDays(rand(1,5));

        $booking->guests = rand(1,4);
        $booking->basePrice = rand(5,10)*10;
        $booking->discount = rand(1,10)*10;
        $booking->deposit = (int)$booking->basePrice/(rand(1,2)*5);

        $booking->comments = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut consequat, nunc ut lobortis dapibus, tortor sapien tristique leo, sed molestie.";

        $booking->customer_id = rand(1,Guest::count());
        $booking->room_id = rand(1,Room::count());

        $booking->save();
    }
}
