<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Booking extends Model
{

    protected $dates = [
        'created_at',
        'updated_at',
        'arrival',
        'departure'
    ];

    /*
     * get main customer of booking
     */
	public function customer() {
		return $this->belongsTo('App\Guest');
	}

	/*
	 * get booked room(s)
	 */
	public function rooms() {
		return $this->belongsToMany('App\Room')->withPivot('bed');
	}

	public function days() {
		return $this->arrival->diffInDays($this->departure);
	}

	/*
	 * number of days to show in current week
	 */
	public function toShow($dates) {
		$start = $week_start = $dates[0]['date']->copy();
		$end   = $week_end   = $dates[count($dates)-1]['date']->copy();

		$week_end->addDay();

		if ($this->arrival->gte($week_start)) {
            $start = $this->arrival;
        }
		if ($this->departure->lte($week_end)){
            $end = $this->departure;
        }
		return $start->diffInDays($end);
	}

	public function color() {
		$name = $this->customer->name;
		$hash = sha1($name.$this->arrival);

		$r = substr($hash, 0,2);
		$g = substr($hash, 2,2);
		$b = substr($hash, 4,2);
		return '#'.$r.$g.$b;
	}

    public function isNow() {
        return Carbon::parse("now")->between($this->arrival, $this->departure);
    }
}
