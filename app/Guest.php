<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use CountryList;

class Guest extends Model
{

	// derived attribute
	protected $appends = ['name'];
	
	public function getNameAttribute() {
		return $this->firstname." ".$this->lastname;
	}

	public function bookings() {
		return $this->hasMany('App\Booking');
	}

	public function getCountryStrAttribute() {
		$country_str = CountryList::find($this->country, app()->getLocale());
		return $country_str;
	}
}
