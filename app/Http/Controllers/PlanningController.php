<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Booking;
use App\Room;

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
            'comments' => 'string',
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

        // if ($booking->rooms[0]->id !== (int)$request->input('room')) {
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
        // }

        // $booking->save();
        return redirect()->route('planning')->withInput();
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

        return redirect()->route('planning')->withInput();
    }
}
