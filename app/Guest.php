<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use CountryList;

class Guest extends Model
{

    // derived attribute
    protected $appends = ['name'];

    public function getNameAttribute()
    {
        return $this->firstname." ".$this->lastname;
    }

    /**
     * bookings for which this guest is the main customer
     */
    public function bookings()
    {
        return $this->hasMany('App\Booking', 'customer_id');
    }

    /**
     * bookings for which this guest is an extra
     */
    public function bookingsAsGuest()
    {
        return $this->belongsToMany('App\Booking');
    }

    /**
     * all bookings
     */
    public function getAllBookings()
    {
        return $this->bookings->concat($this->bookingsAsGuest);
    }

	public function getCountryStrAttribute() {
		$country_str = CountryList::find($this->country, app()->getLocale());
		return $country_str;
	}
}
