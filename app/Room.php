<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class Room extends Model
{

	public function bookings() {
		return $this->belongsToMany('App\Booking')->withPivot('bed');
	}

	public function getCurrentBookings() {
		return $this->bookings->where('arrival', 'after', Carbon::parse("now"));
	}

	public function hasBooking(Carbon $date, $bed)
	{
		foreach ($this->bookings as $booking) {
			if($bed !== $booking->pivot->bed) continue;
			if($date->between($booking->arrival, $booking->departure->subDay())) {
				return $booking;
			}
		}
		return false;
	}

	public function findFreeBeds(Booking $booking) {
		$arrival = $booking->arrival;
		$departure = $booking->departure;

		$overlap = $this->bookings()
			->where('arrival', '<=', $departure)
			->where('departure', '>=', $arrival)
			->get();
		
		$beds_taken = [];
		foreach ($overlap as $o) {
			$beds_taken[] = $o->pivot->bed;
		}

		$beds = array_values(array_diff(range(1,$this->beds), $beds_taken));

		return $beds;
	}

	public function moveUp() {
		$higher = Room::where('sorting', $this->sorting-1)->first();
		if ($higher) {
			$higher->sorting += 1;
			$higher->save();
		}

		$this->sorting--;
		$this->save();
	}

	public function moveDown() {
		$lower = Room::where('sorting', $this->sorting+1)->first();
		if ($lower) {
			$lower->sorting -= 1;
			$lower->save();
		}

		$this->sorting++;
		$this->save();
	}
}
