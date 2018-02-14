<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'room' => 'required',
            'basePrice' => 'required|integer|min:0',
            'discount' => 'required|integer|min:0|max:100',
            'deposit' => 'required|integer|min:0',
            'guests' => 'required|integer|min:1',
            'comments' => 'nullable|string',
        ]);

        $booking->arrival = Carbon::parse($request->input('arrival'));
        $booking->departure = Carbon::parse($request->input('departure'));
        $booking->customer_id = $request->input('customer');
        $booking->guests = $request->input('guests');
        $booking->basePrice = $request->input('basePrice');
        $booking->discount = $request->input('discount');
        $booking->deposit = $request->input('deposit');
        $booking->comments = $request->input('comments');

        $booking->ext_booking = ("no" !== $request->input('ext_booking', 'no'));

        $booking->rooms()->detach();

        $placement = explode(';', $request->input('room'));
        $room_id = (int)$placement[0];
        $part = count($placement) > 1 ? (int)$placement[1] : -1;

        $room = Room::find($room_id);
        $beds = $room->findFreeBeds($booking->arrival, $booking->departure, $part);

        $options['part'] = $part;
        $options['asWhole'] = $request->input('as_whole', 'no') === "yes" ? true : false;

        if (count($beds) >= $booking->guests) {
            $booking->save();
            for ($i=0; $i<count($beds) && $i<$booking->guests; $i++) {
                $booking->rooms()->save($room, ['bed' => $beds[$i], 'options' => $options]);
            }
        } else {
            return redirect()
                    ->route('booking.edit', $booking)
                    ->with('error', 'Geen voldoende bedden')
                    ->withInput();
        }

        return redirect()->route('booking.show', $booking);
    }

    public function createBooking(Request $request) {

        $validatedData = $request->validate([
            'arrival' => 'required|date',
            'departure' => 'required|date|after:arrival',
            'customer' => 'required|exists:guests,id',
            'room' => 'required',
            'basePrice' => 'required|integer|min:0',
            'discount' => 'required|integer|min:0|max:100',
            'deposit' => 'required|integer|min:0',
            'guests' => 'required|integer|min:1',
            'comments' => 'nullable|string',
        ]);

        $booking = new Booking;

        $booking->arrival = Carbon::parse($request->input('arrival'));
        $booking->departure = Carbon::parse($request->input('departure'));
        $booking->customer_id = $request->input('customer');
        $booking->guests = $request->input('guests');
        $booking->basePrice = $request->input('basePrice');
        $booking->discount = $request->input('discount');
        $booking->deposit = $request->input('deposit');
        $booking->comments = $request->input('comments');

        $booking->ext_booking = ("no" !== $request->input('ext_booking', 'no'));

        $placement = explode(';', $request->input('room'));
        $room_id = (int)$placement[0];
        $part = count($placement) > 1 ? (int)$placement[1] : -1;

        $room = Room::find($room_id);
        $beds = $room->findFreeBeds($booking->arrival, $booking->departure, $part);

        $options['part'] = $part;
        $options['asWhole'] = $part === -1 ? true : false;

        if (count($beds) >= $booking->guests) {
            $booking->save();
            for ($i=0; $i<count($beds) && $i<$booking->guests; $i++) {
                $booking->rooms()->save($room, ['bed' => $beds[$i], 'options' => $options]);
            }
        } else {
            return redirect()
                    ->route('booking.create')
                    ->with('error', 'Geen voldoende bedden')
                    ->withInput();
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

        $bookings = Booking::where('arrival', '<=', $end)
                        ->where('departure', '>=', $start)
                        ->orderBy('arrival')
                        ->get();

        return $bookings;
    }

    public function search(Request $request)
    {
        $search = $request->query('search', '');
        $sql_search = '%'.$search.'%';
        // DB::enableQueryLog();
        $bookings = Booking::join('guests', 'guests.id', '=', 'bookings.customer_id')
                    ->select('bookings.*', 'guests.firstname', 'guests.lastname')
                    ->where('firstname', 'LIKE', $sql_search)
                    ->orwhere('lastname', 'LIKE', $sql_search)
                    ->orwhere(DB::raw('CONCAT(firstname, " ", lastname)'), 'LIKE', $sql_search)
                    ->orderBy('arrival')
                    ->get();
        // dd(DB::getQueryLog());
        return view('planning.search', [
            'bookings' => $bookings
        ]);
    }
}
