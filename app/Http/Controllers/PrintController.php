<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Note;
use App\Room;

class PrintController extends Controller
{
    public function print(Request $request)
    {
        Carbon::setWeekStartsAt(Carbon::SATURDAY);
        $date = (new Carbon($request->query('date', "now")))->startOfWeek();

        $note = Note::where('week_start', $date)->first();

        $dates = [];
        for ($i=0; $i < 7; $i++) {
            $dates[$i] = [
                'day' => $date->formatLocalized('%a'),
                'date_str' => $date->format('d/m/Y'),
                'date' => new Carbon($date)
            ];
            $date->modify('+1 day');
        }

        // retrieve all rooms and eager load all bookings for visible week
        $rooms = Room::orderBy('sorting')
            ->with(['bookings' => function ($query) use ($dates) {
                $query
                ->with('customer')
                ->with('extraGuests')
                ->whereDate('arrival', '<=', $dates[6]['date'])
                ->whereDate('departure', '>=', $dates[0]['date']);
            }])
            ->get();

        return view('planning.print', [
            'rooms' => $rooms,
            'dates' => $dates,
            'note' => $note,
        ]);
    }
}
