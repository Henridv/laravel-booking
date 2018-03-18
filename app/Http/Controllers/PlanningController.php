<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

use App\Booking;
use App\Extra;
use App\Guest;
use App\Room;

use CountryList;

class PlanningController extends Controller
{
    public function saveBooking(Request $request, Booking $booking)
    {
        $validatedData = $request->validate([
            'arrival' => 'required|date',
            'arrivalTime' => 'required|date_format:"H:i"',
            'departure' => 'required|date|after:arrival',
            'customer' => 'required|exists:guests,id',
            'room' => 'required',
            'basePrice' => 'required|integer|min:0',
            'discount' => 'required|integer|min:0|max:100',
            'deposit' => 'required|integer|min:0',
            'guests' => 'required|integer|min:1',
            'composition' => 'nullable|string',
            'comments' => 'nullable|string',
        ]);

        $booking->arrival = Carbon::parse($request->input('arrival').' '.$request->input('arrivalTime'));
        $booking->departure = Carbon::parse($request->input('departure'));
        $booking->customer_id = $request->input('customer');
        $booking->basePrice = $request->input('basePrice');
        $booking->discount = $request->input('discount');
        $booking->deposit = $request->input('deposit');
        $booking->composition = $request->input('composition');
        $booking->comments = $request->input('comments');

        $booking->ext_booking = ("no" !== $request->input('ext_booking', 'no'));

        $placement = explode(';', $request->input('room'));
        $room_id = (int)$placement[0];
        $part = count($placement) > 1 ? (int)$placement[1] : -1;
        $guests = $request->input('guests');

        // only look for free beds if:
        // 1. new booking; or
        // 2. existing booking and number of guests or room changes
        if (!$booking->exists || ($guests !== $booking->guests || $room_id !== $booking->rooms[0]->room_id)) {
            $room = Room::find($room_id);
            $beds = $room->findFreeBeds($booking, $part);

            $options['part'] = $part;
            $options['asWhole'] = $request->input('as_whole', 'no') === "yes" ? true : false;

            if (count($beds) >= $guests) {
                $booking->guests = $guests;
                $booking->save();

                $options['beds'] = array_slice($beds, 0, $guests);
                $booking->rooms()->sync([$room_id => ['bed' => 1, 'options' => $options]]);
            } else {
                return null;
            }
        }
        return true;
    }

    public function editBooking(Request $request, Booking $booking)
    {
        $result = $this->saveBooking($request, $booking);

        if ($result) {
            return redirect()->route('booking.show', $booking);
        } else {
            return redirect()
                ->route('booking.edit', $booking)
                ->with('error', 'Geen voldoende bedden')
                ->withInput();
        }
    }

    public function createBooking(Request $request)
    {
        $booking = new Booking;
        $result = $this->saveBooking($request, $booking);

        if ($result) {
            return redirect()
                ->route('planning', ['date' => $booking->arrival->toDateString()]);
        } else {
            return redirect()
                ->route('booking.create')
                ->with('error', 'Geen voldoende bedden')
                ->withInput();
        }
    }

    public function editGuest(Request $request, Booking $booking, Guest $guest)
    {

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

    /**
     * Delete extra guest
     */
    public function delExtraGuest(Request $request, Booking $booking, Guest $guest)
    {
        $booking->extraGuests()->detach($guest);
        return redirect()->route('booking.show', $booking);
    }

    /**
     * Delete extra
     */
    public function delExtra(Request $request, Booking $booking, Extra $extra)
    {
        $booking->extras()->detach($extra);
        return redirect()->route('booking.show', $booking);
    }

    /**
     * Get bookings in the following period
     *
     * @param int $periodInWeeks Period in number of weeks
     *
     * @return Booking[] Array of bookings in period
     */
    public static function getBookings($periodInWeeks)
    {
        $start = new Carbon('now');
        $end   = $start->copy()->addWeeks($periodInWeeks);

        $bookings = Booking::with(['customer', 'rooms'])
                        ->where('arrival', '<=', $end)
                        ->where('departure', '>=', $start)
                        ->orderBy('arrival')
                        ->get();

        return $bookings;
    }

    public function search(Request $request)
    {
        $search = $request->query('search', '');
        $sql_search = '%'.$search.'%';

        $bookings = Booking::join('guests', 'guests.id', '=', 'bookings.customer_id')
                    ->select('bookings.*', 'guests.firstname', 'guests.lastname')
                    ->where('firstname', 'LIKE', $sql_search)
                    ->orwhere('lastname', 'LIKE', $sql_search)
                    ->orwhere(DB::raw('CONCAT(firstname, " ", lastname)'), 'LIKE', $sql_search)
                    ->orderBy('arrival')
                    ->get();

        return view('planning.search', [
            'bookings' => $bookings
        ]);
    }

    public static function getCountryList($locale = 'nl_BE')
    {
        $countries = CountryList::all($locale);

        // move most common picks to front of array
        $be = $countries['BE'];
        $fr = $countries['FR'];
        $nl = $countries['NL'];
        $es = $countries['ES'];
        $nope = "---------------";

        $countries = [
            'BE' => $be,
            'FR' => $fr,
            'NL' => $nl,
            'ES' => $es,
            $nope
        ] + $countries;

        return $countries;
    }
}
