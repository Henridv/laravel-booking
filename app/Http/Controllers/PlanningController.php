<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Carbon\Carbon;

use App\Booking;
use App\Room;
use App\Guest;

class PlanningController extends Controller
{
    public function editBooking(Request $request, Booking $booking) {

        $validatedData = $request->validate([
            'arrival' => 'required|date',
            'departure' => 'required|date|after:arrival',
            'customer' => 'required|exists:guests,id',
            'room' => 'required|exists:rooms,id',
            'basePrice' => 'required|integer|min:0',
            'discount' => 'required|integer|min:0|max:100',
            'deposit' => 'required|integer|min:0',
            'guests' => 'required|integer|min:1',
            'comments' => 'nullable|string',
        ]);

        $booking->arrival = $request->input('arrival');
        $booking->departure = $request->input('departure');
        $booking->customer_id = $request->input('customer');
        $booking->guests = $request->input('guests');
        $booking->basePrice = $request->input('basePrice');
        $booking->discount = $request->input('discount');
        $booking->deposit = $request->input('deposit');
        $booking->comments = $request->input('comments');

        $booking->white = ("no" === $request->input('isyes', 'no'));

        $booking->rooms()->detach();
            $room = Room::find($request->input('room'));
            $beds = $room->findFreeBeds($booking);

            if (count($beds) >= $booking->guests) {
                $booking->save();
                for ($i=0; $i<count($beds) && $i<$booking->guests; $i++) {
                    $booking->rooms()->save($room, ['bed' => $beds[$i]]);
                }
            } else {
                echo 'ERROOOOOOOR';
                die();
            }

        return redirect()->route('booking.show', $booking);
    }

    public function createBooking(Request $request) {

        $validatedData = $request->validate([
            'arrival' => 'required|date',
            'departure' => 'required|date|after:arrival',
            'customer' => 'required|exists:guests,id',
            'room' => 'required|exists:rooms,id',
            'basePrice' => 'required|integer|min:0',
            'discount' => 'required|integer|min:0|max:100',
            'deposit' => 'required|integer|min:0',
            'guests' => 'required|integer|min:1',
            'comments' => 'nullable|string',
        ]);

        $booking = new Booking;

        $booking->arrival = $request->input('arrival');
        $booking->departure = $request->input('departure');
        $booking->customer_id = $request->input('customer');
        $booking->guests = $request->input('guests');
        $booking->basePrice = $request->input('basePrice');
        $booking->discount = $request->input('discount');
        $booking->deposit = $request->input('deposit');
        $booking->comments = $request->input('comments');

        $booking->white = ("no" === $request->input('isyes', 'no'));

        $room = Room::find($request->input('room'));
        $beds = $room->findFreeBeds($booking);

        if (count($beds) >= $booking->guests) {
            $booking->save();
            for ($i=0; $i<count($beds) && $i<$booking->guests; $i++) {
                $booking->rooms()->save($room, ['bed' => $beds[$i]]);
            }
        } else {
            echo 'ERROOOOOOOR';
            die();
        }

        return redirect()
            ->route('planning',['date' => $booking->arrival->toDateString()]);
    }

    public function editGuest(Request $request, Booking $booking, Guest $guest) {

        $validatedData = $request->validate([
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'country' => 'required|string|size:2',
        ]);

        $guest->firstname = $request->input('firstname');
        $guest->lastname = $request->input('lastname');
        $guest->email = $request->input('email');
        $guest->phone = $request->input('phone');
        $guest->country = $request->input('country');

        $guest->save();

        return redirect()->route('booking.show', $booking->id);
    }

    public static function getBookings($periodInWeeks) {
        $start = new Carbon('now');
        $end   = $start->copy()->addWeeks($periodInWeeks);

        $bookings = Booking::where('arrival', '>=', $start)
                        ->where('departure', '<=', $end)
                        ->orderBy('arrival')
                        //->orderBy('lastname')
                        ->get();

        return $bookings;
    }
}
