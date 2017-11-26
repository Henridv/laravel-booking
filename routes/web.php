<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Http\Request;
use App\Room;
use App\Guest;

use Carbon\Carbon;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::prefix('planning')->group(function() {

	Route::get('/', function(Request $request) {
		$rooms = Room::orderBy('name')->get();
		
		Carbon::setWeekStartsAt(Carbon::SATURDAY);
		$date = (new Carbon($request->query('date', "now")))->startOfWeek();
		$dates = [];
		for ($i=0; $i < 7; $i++) { 
			$dates[$i] = ['day' => $date->format('D'), 'date_str' => $date->format('d/m/Y'), 'date' => new Carbon($date)];
			$date->modify('+1 day');
		}

		return view('planning.index', ['rooms' => $rooms, 'dates' => $dates]);
	})->name('planning');

	Route::get('nieuw', function() {
		$countries = CountryList::all('nl_BE');
		
		// move most common picks to front of array
		$be = $countries['BE'];
		$fr = $countries['FR'];
		$nl = $countries['NL'];
		$es = $countries['ES'];
		$nope = "---------------";

		$countries = ['BE' => $be, 'FR' => $fr, 'NL' => $nl, 'ES' => $es, $nope] + $countries;
		
		return view('planning.create', [
			'rooms' => Room::orderBy('name')->get(),
			'guests' => Guest::orderBy('lastname')->get(),
			'countries' => $countries,
		]);
	})->name('booking.create');

	Route::post('nieuw', 'PlanningController@createBooking');

	Route::get('edit/{booking}', function(App\Booking $booking) {
		$countries = CountryList::all('nl_BE');
		
		// move most common picks to front of array
		$be = $countries['BE'];
		$fr = $countries['FR'];
		$nl = $countries['NL'];
		$es = $countries['ES'];
		$nope = "---------------";

		$countries = ['BE' => $be, 'FR' => $fr, 'NL' => $nl, 'ES' => $es, $nope] + $countries;

		return view('planning.create', [
			'rooms' => Room::orderBy('name')->get(),
			'guests' => Guest::orderBy('lastname')->get(),
			'booking' => $booking,
			'countries' => $countries,
		]);
	})->name('booking.edit');

	Route::post('edit/{booking}', 'PlanningController@editBooking');
	
	Route::get('del/{booking}', function(App\Booking $booking) {
		$booking->delete();
		
		return redirect()->route('planning');
	})->name('booking.delete');

	Route::get('getGuests', 'AjaxController@getGuests')->name('booking.ajax.guests');
	Route::post('saveGuest', 'AjaxController@saveGuest')->name('booking.ajax.guest.save');
});

Route::prefix('kamers')->group(function() {
	Route::get('/', function() {
		$rooms = App\Room::orderBy('name')->get();
		return view('rooms.index', ['rooms' => $rooms]);
	})->name('rooms');

	Route::get('toevoegen', function() {
		return view('rooms.add');
	})->name('room.add');
	
	Route::post('toevoegen', function(Request $request) {
		$name = $request->input('name');
		$beds = (int)$request->input('beds');

		$r = new Room;
		$r->name = $name;
		$r->beds = $beds;
		$r->save();

		return redirect()->route('rooms');
	});

	Route::get('edit/{room}', function(App\Room $room) {
		return view('rooms.add', ['room' => $room]);
	})->name('room.edit');

	Route::post('edit/{room}', function(App\Room $room, Request $request) {
		$name = $request->input('name');
		$beds = (int)$request->input('beds');

		$room->name = $name;
		$room->beds = $beds;
		$room->save();

		return redirect()->route('rooms');
	});

	Route::get('del/{room}', function(App\Room $room) {
		$room->delete();
		return redirect()->route('rooms');
	})->name('room.del');
});

Route::get('extras', function() {
	return view('welcome');
})->name('extra');
