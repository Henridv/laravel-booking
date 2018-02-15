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
        return $this->belongsToMany('App\Room')
            ->as('properties')
            ->withPivot(['bed', 'options'])
            ->using('App\BookingProperties');
    }

	public function days() {
		return $this->arrival->diffInDays($this->departure);
	}

	/**
	 * number of days to show in current week
     *
     * @param array $dates Dates in visibile week
     *
     * @return int Number of visible days for this booking
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
        $color = $this->customer->color;

        $r = substr($color, 1,2);
        $g = substr($color, 3,2);
        $b = substr($color, 5,2);
        $luma = (float)0.2126 * hexdec($r)
            + 0.7152 * hexdec($g)
            + 0.0722 * hexdec($b);

        return ['color' => $this->customer->color, 'luma' => $luma];
	}

    public function isNow() {
        return Carbon::parse("now")->between($this->arrival, $this->departure);
    }
}
