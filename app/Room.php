<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Room extends Model
{

	public function bookings() {
		return $this->hasMany('App\Booking');
	}

	public function getCurrentBookings() {
		return $this->bookings->where('arrival', 'after', Carbon::parse("now"));
	}

	public function hasBooking(Carbon $date, $bed)
	{
		foreach ($this->bookings as $booking) {
			//if(!in_array($bed, $booking['beds'])) continue;
			if($date->between($booking->arrival, $booking->departure)) {
				return $booking;
			}
		}
		return false;
	}
}
