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

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('planning', function() {
	return view('welcome');
})->name('planning');

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
