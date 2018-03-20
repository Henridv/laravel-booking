<?php

namespace App\Listeners;

use App\Events\BookingChangedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrganizeRoomListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  BookingDeletedEvent  $event
     * @return void
     */
    public function handle(BookingChangedEvent $event)
    {
        $room = $event->room;
        $arrival = $event->arrival;
        $departure = $event->departure;

        if ($room) {
            $room->organize($arrival, $departure);
        }
        return;
    }
}
