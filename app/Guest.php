<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{

	protected $appends = ['name'];

	public function getNameAttribute() {
		return $this->firstname." ".$this->lastname;
	}
}
