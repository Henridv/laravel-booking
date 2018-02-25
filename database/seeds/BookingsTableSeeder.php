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

        for ($i=0; $i<5; $i++) {
            $booking = new Booking();

            $booking->arrival = Carbon::parse('now')->startOfWeek()->addDays(rand(0,5))->addHours(rand(0,23));
            $booking->departure = Carbon::parse($booking->arrival)->addDays(rand(1,10));

            $room = Room::find(rand(1,Room::count()));
            
            $booking->guests = $guests = rand(1,$room->beds);
            $booking->basePrice = rand(5,10)*10;
            $booking->discount = rand(1,10)*10;
            $booking->deposit = (int)$booking->basePrice/(rand(1,2)*5);
            $booking->ext_booking = rand(0,1);
            $booking->comments = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut consequat, nunc ut lobortis dapibus, tortor sapien tristique leo, sed molestie.";
            $booking->composition = "Lorem ipsum dolor sit amet, consectetur adipiscing elit.";
            $booking->customer_id = rand(1,Guest::count());

            $room_id = $room->id;
            $part = -1;

            $beds = $room->findFreeBeds($booking, $part);

            $options['part'] = $part;
            $options['asWhole'] = rand(0,1) === 1 ? true : false;

            if (count($beds) >= $guests) {
                $booking->save();
                
                $options['beds'] = array_slice($beds, 0, $guests);
                $booking->rooms()->sync([$room_id => ['bed' => 1, 'options' => $options]]);
            }

        }
    }
}
