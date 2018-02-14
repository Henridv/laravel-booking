<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class BookingProperties extends Pivot
{
    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'options' => 'array',
    ];

    protected $fillable = [
        'bed', 'options'
    ];

    public function booking() {
        return $this->belongsTo('App\Booking');
    }
    public function room() {
        return $this->belongsTo('App\Room');
    }

    // public function getBedAttribute() {
    //     return $this->bed;
    // }

    public function freeBeds($part = -1) {
        if ($part === -1) {
            $beds_taken = [];
            foreach ($this->bookings as $o) {
                $beds_taken[] = $o->pivot->bed;
            }

            $beds = array_values(array_diff(range(1,$this->beds), $beds_taken));

            return $beds;
        }
    }
}
