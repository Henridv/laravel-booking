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
use App\Booking;
use App\Guest;

use App\Http\Controllers\PlanningController;

use Carbon\Carbon;

Route::get('/', function () {
	$bookings = PlanningController::getBookings(2);

    return view('welcome', [
    	"bookings" => $bookings
    ]);
})->name('welcome');

Route::prefix('planning')->group(function() {

	Route::get('/', function(Request $request) {
		$rooms = Room::orderBy('sorting')->get();
		
		// setlocale(LC_TIME, app()->getlocale());
		Carbon::setWeekStartsAt(Carbon::SATURDAY);
		$date = (new Carbon($request->query('date', "now")))->startOfWeek();
		$dates = [];
		for ($i=0; $i < 7; $i++) { 
			$dates[$i] = [
				'day' => $date->formatLocalized('%a'),
				'date_str' => $date->format('d/m/Y'),
				'date' => new Carbon($date)
			];
			$date->modify('+1 day');
		}

		return view('planning.index', ['rooms' => $rooms, 'dates' => $dates]);
	})->name('planning');

	Route::post('goto_date', function(Request $request) {
		$date = Carbon::parse($request->input('goto_date', "now"))->toDateString();
		return redirect()->route('planning', ['date' => $date]);
	})->name('planning.change_date');
	
	Route::get('nieuw', function(Request $request) {
		$countries = CountryList::all('nl_BE');
		
		Carbon::setWeekStartsAt(Carbon::SATURDAY);
		$date = (new Carbon($request->query('date', "now")))->startOfWeek();
		if(Carbon::parse("now")->gt($date)) {
			$date->addWeek();
		}

		// move most common picks to front of array
		$be = $countries['BE'];
		$fr = $countries['FR'];
		$nl = $countries['NL'];
		$es = $countries['ES'];
		$nope = "---------------";

		$countries = [
			'BE' => $be,
			'FR' => $fr,
			'NL' => $nl,
			'ES' => $es,
			$nope
		] + $countries;
		
		return view('planning.create', [
			'rooms' => Room::orderBy('sorting')->get(),
			'guests' => Guest::orderBy('lastname')->get(),
			'countries' => $countries,
			'date' => $date,
		]);
	})->name('booking.create');

	Route::post('nieuw', 'PlanningController@createBooking');

	Route::get('edit/{booking}', function(Booking $booking) {
		$countries = CountryList::all(app()->getLocale());
		
		// move most common picks to front of array
		$be = $countries['BE'];
		$fr = $countries['FR'];
		$nl = $countries['NL'];
		$es = $countries['ES'];
		$nope = "---------------";

		$countries = [
			'BE' => $be,
			'FR' => $fr,
			'NL' => $nl,
			'ES' => $es,
			$nope
		] + $countries;

		return view('planning.create', [
			'rooms' => Room::orderBy('sorting')->get(),
			'guests' => Guest::orderBy('lastname')->get(),
			'booking' => $booking,
			'countries' => $countries,
		]);
	})->name('booking.edit');

	Route::post('edit/{booking}', 'PlanningController@editBooking');

	Route::get('{booking}', function(App\Booking $booking) {
		
		return view('planning.show', ["booking" => $booking]);
	})->name('booking.show');

	Route::get('del/{booking}', function(App\Booking $booking) {
		$booking->delete();
		
		return redirect()->route('planning');
	})->name('booking.delete');

	Route::get('getGuests', 'AjaxController@getGuests')
		->name('booking.ajax.guests');
		
	Route::post('saveGuest', 'AjaxController@saveGuest')
		->name('booking.ajax.guest.save');
});

Route::prefix('gast')->group(function() {

	Route::get('edit/{booking}/{guest}', function(App\Booking $booking, App\Guest $guest) {
		$countries = CountryList::all(app()->getLocale());
		
		// move most common picks to front of array
		$be = $countries['BE'];
		$fr = $countries['FR'];
		$nl = $countries['NL'];
		$es = $countries['ES'];
		$nope = "---------------";

		$countries = [
			'BE' => $be,
			'FR' => $fr,
			'NL' => $nl,
			'ES' => $es,
			$nope
		] + $countries;

		return view('guests.create', [
			'guest' => $guest,
			'countries' => $countries,
			'booking' => $booking,
		]);
	})->name('guest.edit');

	Route::post('edit/{booking}/{guest}', 'PlanningController@editGuest');

	Route::get('del/{guest}', function(App\Guest $guest) {
		$guest->delete();
		
		return redirect()->route('planning');
	})->name('guest.delete');
});

Route::prefix('kamers')->group(function() {
	Route::get('/', function() {
		$rooms = Room::orderBy('sorting')->get();
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

	Route::get('sort/up/{room}', function(Room $room) {
		$room->moveUp();
		return redirect()->route('rooms');
	})->name('room.sort.up');

	Route::get('sort/down/{room}', function(Room $room) {
		$room->moveDown();
		return redirect()->route('rooms');
	})->name('room.sort.down');
});

Route::get('extras', function() {
	return view('welcome');
})->name('extra');
