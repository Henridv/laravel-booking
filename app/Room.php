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
		return $this->belongsToMany('App\Booking')->withPivot('bed');
	}

	// getter for layout
	public function getLayoutStrAttribute() {
		return implode(', ',$this->layout);
	}

	public function getCurrentBookings() {
		return $this->bookings->where('arrival', 'after', Carbon::parse("now"));
	}

	public function hasBooking(Carbon $date, $bed = "all")
	{
		$bookings = $this->bookings()
			->where('arrival', '<=', $date)
			->where('departure', '>', $date);

		if ($bed === "all") {
			return $bookings->get()->groupBy('id');
		} else {
			return $bookings->wherePivot('bed', $bed)->first();
		}
	}

	public function findFreeBeds(Booking $booking) {
		$arrival = $booking->arrival;
		$departure = $booking->departure;

		$overlap = $this->bookings()
			->where('arrival', '<', $departure)
			->where('departure', '>', $arrival)
			->get();

		$beds_taken = [];
		foreach ($overlap as $o) {
			$beds_taken[] = $o->pivot->bed;
		}

		$beds = array_values(array_diff(range(1,$this->beds), $beds_taken));

		return $beds;
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
