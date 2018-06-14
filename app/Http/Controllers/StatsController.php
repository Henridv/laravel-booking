<?php

namespace App\Http\Controllers;

use App\Booking;
use App\Room;

use Carbon\Carbon;
use Illuminate\Http\Request;

use CountryList;

class StatsController extends Controller
{
    public const GUESTS_PER_COUNTRY = 'countries';
    public const NO_OF_NIGHTS = 'no_of_nights';

    public function index(Request $request)
    {
        $from_date = new Carbon($request->query('from', "now"));
        $to_date = new Carbon($request->query('to', $from_date->copy()->addMonth()));

        $type = $request->query('type', self::GUESTS_PER_COUNTRY);

        $rooms = Room::all();
        $selectedRooms = $request->query('rooms', []);

        $stats = $this->getStats($type, $from_date, $to_date, $selectedRooms);

        return view('stats.index', compact(
            'type',
            'stats',
            'from_date',
            'to_date',
            'rooms',
            'selectedRooms'
        ));
    }

    /**
     * Generate stats based on type
     */
    public function getStats($type, $from, $to, $rooms)
    {
        switch ($type) {
            case self::GUESTS_PER_COUNTRY:
                $bookings = Booking::getInRange($from, $to)
                    ->with('customer');

                if ($rooms && count($rooms) > 0) {
                    $bookings = $bookings
                        ->whereHas('rooms', function ($query) use ($rooms) {
                            $query->whereIn('room_id', $rooms);
                        });
                }

                $bookings = $bookings->get();

                $bookings_per_country = $bookings->groupBy('customer.country');

                $stats = $bookings_per_country->map(function ($i, $k) {
                    $bookings = $i->count();
                    $guests = $i->sum('guests');
                    $guests_per_booking = $guests/$bookings;
                    return [
                        'country_name' => CountryList::find($k, app()->getLocale()),
                        'bookings' => $bookings,
                        'guests' => $guests,
                        'guests_per_booking' => $guests_per_booking];
                })->sortByDesc('guests');

                $totals = [
                    'bookings' => $stats->sum('bookings'),
                    'guests' => $stats->sum('guests'),
                    'guests_per_booking' => $stats->sum('guests') / $stats->sum('bookings'),
                ];
                $stats = $stats->merge(['totals' => $totals]);

                return $stats;

            case self::NO_OF_NIGHTS:
                $bookings = Booking::getInRange($from, $to)
                    ->with('customer');

                if ($rooms && count($rooms) > 0) {
                    $bookings = $bookings
                        ->whereHas('rooms', function ($query) use ($rooms) {
                            $query->whereIn('room_id', $rooms);
                        });
                }

                $bookings = $bookings->get();

                $days = ['nights' => $bookings->sum('total_nights')];

                return $days;
            default:
                return null;
        }
    }
}
