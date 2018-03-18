<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Booking;

class Extra extends Model
{
    public function bookings()
    {
        return $this->belongsToMany(Booking::class);
    }
}
