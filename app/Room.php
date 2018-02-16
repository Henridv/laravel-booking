<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

class Room extends Model
{
    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'layout' => 'array',
	];

	public function bookings() {
        return $this->belongsToMany('App\Booking')
            ->as('properties')
            ->withPivot(['bed', 'options'])
            ->using('App\BookingProperties');
	}

	// getter for layout
	public function getLayoutStrAttribute() {
		return implode(', ',$this->layout);
	}

	// getter for cumulative layout
	public function getLayoutSplitsAttribute() {
		$cumul = [];
		$total = 0;
		foreach ($this->layout as $l) {
			$total += $l;
			$cumul[] = $total;
		}
		return $cumul;
	}

	public function getCurrentBookings() {
		return $this->bookings->where('arrival', 'after', Carbon::parse("now"));
	}

    /**
     * Determines if there is a booking in this room for provided date and bed.
     *
     * @param Carbon $date
     * @param string $bed
     *
     * @return array array of booking for this date
     */
    public function hasBooking(Carbon $date, $bed = "all")
    {
        $bookings = $this->bookings
            ->where('arrival', '<=', $date->endOfDay())
            ->where('departure', '>', $date);

        if ($bed === "all") {
            return $bookings->groupBy('id');
        } else {
            foreach($bookings as $booking) {
                if (in_array($bed, $booking->properties->options['beds'])) {
                    return $booking;
                }
            }
        }
        return null;
    }

    public function isBookedAsWhole(Carbon $date)
    {
        $booking = $this->bookings
            ->where('arrival', '<=', $date->endOfDay())
            ->where('departure', '>', $date)
            ->first();

        if ($booking) {
            return $booking->properties->options['asWhole'];
        } else {
            return false;
        }
    }

    /**
     * Find free beds in specified period
     *
     * @param string $arrival Arrival date
     * @param string $departure Departure date
     * @param int $part Part of room to find beds
     *
     * @return array Returns an array of free beds in the room for specified dates
     */
    public function findFreeBeds($booking, $part = -1) {

        // find bookings in this room with overlapping dates
        $overlap = $this->bookings()
            ->where('arrival', '<', $booking->departure->startOfDay())
            ->where('departure', '>', $booking->arrival->startOfDay())
            ->where('booking_id', '!=', $booking->id)
            ->get();

        // which beds are taken
        $beds_taken = [];
        foreach ($overlap as $o) {
            if ($o->properties->options['asWhole'])
                return [];
            else
                $beds_taken = array_merge($beds_taken, $o->properties->options['beds']);
        }
        $beds_taken = array_unique($beds_taken);

		// get all beds in specific part
		$all_beds = range(1,$this->beds);
		if ($part !== -1) {
			$beds_in_part = [];
			foreach($this->layout as $l) {
				$beds_in_part[] = array_splice($all_beds, 0, $l);
			}
		}
		$potential = ($part === -1) ? $all_beds : $beds_in_part[$part];

		$beds_available = array_values(array_diff($potential, $beds_taken));

		return $beds_available;
	}

	public function moveUp() {
		$higher = Room::where('sorting', '<', $this->sorting)
					->orderBy('sorting', 'desc')->first();
		if ($higher) {
			$higher->sorting = $this->sorting;
			$higher->save();
		}

		$this->sorting--;
		$this->save();
	}

	public function moveDown() {
		$lower = Room::where('sorting', '>', $this->sorting)
					->orderBy('sorting')->first();
		if ($lower) {
			$lower->sorting = $this->sorting;
			$lower->save();
		}

		$this->sorting++;
		$this->save();
	}

	public static function getMaxBeds() {
		return Room::max('beds');
	}
}
