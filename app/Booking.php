<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

use App\Events\BookingUpdatedEvent;
use App\Scopes\RoleScope;

class Booking extends Model
{

    protected $dispatchesEvents = [
        'updated' => BookingUpdatedEvent::class
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'arrival',
        'departure'
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new RoleScope);
    }

    /**
     * get main customer of booking
     */
    public function customer()
    {
        return $this->belongsTo('App\Guest');
    }

    /**
     * extra guests of the booking
     */
    public function extraGuests()
    {
        return $this->belongsToMany('App\Guest');
    }

    public function getGuest($bed)
    {
        $first_bed = $this->properties->options['beds'][0];
        $i = $bed - $first_bed + 1;
        if ($i === 0 || $i > count($this->extraGuests)) {
            return $this->customer;
        } else {
            return $this->extraGuests->get($i-1);
        }
    }

    public function isFirst($bed)
    {
        $first_bed = $this->properties->options['beds'][0];
        $i = $bed - $first_bed + 1;
        return $i === 0;
    }

    /**
     * get booked room(s)
     */
    public function rooms()
    {
        return $this->belongsToMany('App\Room')
            ->as('properties')
            ->withPivot(['bed', 'options'])
            ->using('App\BookingProperties');
    }

    public function extras()
    {
        return $this->belongsToMany('App\Extra')->withPivot('amount');
    }

    /**
     * duration of booking in days
     */
    public function days()
    {
        return $this->arrival->diffInDays($this->departure);
    }

    /**
     * get tooltip
     */
    public function getTooltipAttribute()
    {
        return view('components.tooltip', ["tooltip" => $this])->render();
    }

    /**
     * get remaining value to pay
     */
    public function getRemainingAttribute()
    {
        return $this->basePrice*(100-$this->discount)/100.0 - $this->deposit;
    }

    /**
     * number of days to show in current week
     *
     * @param array $dates Dates in visibile week
     *
     * @return int Number of visible days for this booking
     */
    public function toShow($dates)
    {
        $start = $week_start = $dates[0]['date']->copy();
        $end   = $week_end   = $dates[count($dates)-1]['date']->copy();

        $week_end->addDay();

        if ($this->arrival->gte($week_start)) {
            $start = $this->arrival;
        }
        if ($this->departure->lte($week_end)) {
            $end = $this->departure;
        }
        return $start->startOfDay()
                ->diffInDays($end->startOfDay());
    }

    /**
     * get color id and luma for this booking
     */
    public function color()
    {
        $color = $this->customer->color;

        $r = substr($color, 1, 2);
        $g = substr($color, 3, 2);
        $b = substr($color, 5, 2);
        $luma = (float)0.2126 * hexdec($r)
            + 0.7152 * hexdec($g)
            + 0.0722 * hexdec($b);

        return ['color' => $this->customer->color, 'luma' => $luma];
    }

    /**
     * check if booking is now
     */
    public function isNow()
    {
        return Carbon::parse("now")->between($this->arrival, $this->departure);
    }
}
