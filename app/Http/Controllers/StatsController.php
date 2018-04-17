<?php

namespace App\Http\Controllers;

use App\Booking;

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
        $stats = $this->getStats($type, $from_date, $to_date);

        return view('stats.index', [
            'type' => $type,
            'stats' => $stats,
            'from_date' => $from_date,
            'to_date' => $to_date,
        ]);
    }

    /**
     * Generate stats based on type
     */
    public function getStats($type, $from, $to)
    {
        switch ($type) {
            case self::GUESTS_PER_COUNTRY:
                $bookings = Booking::getInRange($from, $to)->with('customer')->get();
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
                $bookings = Booking::getInRange($from, $to)->with('customer')->get();

                $days = ['nights' => $bookings->sum('total_nights')];

                return $days;
            default:
                return null;
        }
    }
}
