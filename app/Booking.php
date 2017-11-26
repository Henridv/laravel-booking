<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{

    protected $dates = [
        'created_at',
        'updated_at',
        'arrival',
        'departure'
    ];

	public function customer() {
		return $this->belongsTo('App\Guest');
	}

	public function days() {
		return $this->arrival->diffInDays($this->departure);
	}
}
