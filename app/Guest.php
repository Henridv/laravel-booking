<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
}
