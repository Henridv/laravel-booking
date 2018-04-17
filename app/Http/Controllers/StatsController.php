<?php

namespace App\Http\Controllers;

use App\Booking;

use Carbon\Carbon;
use Illuminate\Http\Request;

use CountryList;

class StatsController extends Controller
{
    public const GUESTS_PER_COUNTRY = 'countries';
    public const BOOKINGS_PER_COUNTRY = 'bookings_per_country';

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
            case self::BOOKINGS_PER_COUNTRY:
                $bookings = Booking::getInRange($from, $to)->with('customer')->get();
                $bookings_per_country = $bookings->groupBy('customer.country');

                $countries = $bookings_per_country->map(function ($i, $k) {
                    return [
                        'country_name' => CountryList::find($k, app()->getLocale()),
                        'count' => $i->sum('guests')];
                })->sortByDesc('count');
                return $countries;

            case self::GUESTS_PER_COUNTRY:
                $bookings = Booking::getInRange($from, $to)->with('customer')->get();
                $bookings_per_country = $bookings->groupBy('customer.country');

                $countries = $bookings_per_country->map(function ($i, $k) {
                    $bookings = $i->count();
                    $guests = $i->sum('guests');
                    $guests_per_booking = $guests/$bookings;
                    return [
                        'country_name' => CountryList::find($k, app()->getLocale()),
                        'bookings' => $bookings,
                        'guests' => $guests,
                        'guests_per_booking' => $guests_per_booking];
                })->sortByDesc('guests');
                return $countries;

            default:
                return null;
        }
    }
}
