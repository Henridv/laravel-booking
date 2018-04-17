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
use App\Extra;
use App\Guest;
use App\User;
use App\Role;
use App\Note;

use App\Events\BookingDeletedEvent;

use App\Http\Controllers\PlanningController;

use Carbon\Carbon;

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/', 'PlanningController@upcoming')->name('welcome');

    /**
     * PLANNING
     */
    Route::prefix('planning')->group(function () {

        Route::get('/', function (Request $request) {

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

            return view('planning.index', [
                'rooms' => $rooms,
                'dates' => $dates,
                'note' => $note,
            ]);
        })->name('planning');

        Route::post('goto_date', function (Request $request) {
            $date = Carbon::parse($request->input('goto_date', "now"))->toDateString();
            return redirect()->route('planning', ['date' => $date]);
        })->name('planning.change_date');

        Route::get('nieuw', function (Request $request) {
            $countries = PlanningController::getCountryList('nl_BE');

            Carbon::setWeekStartsAt(Carbon::SATURDAY);
            $date = (new Carbon($request->query('date', "now")));

            $room_id = $request->query('room', 0);
            $part = $request->query('part', -1);

            return view('planning.create', [
                'rooms' => Room::orderBy('sorting')->get(),
                'guests' => Guest::orderBy('lastname')->get(),
                'countries' => $countries,
                'date' => $date,
                'room_id' => $room_id,
                'part' => $part,
                'max_beds' => Room::getMaxBeds(),
            ]);
        })->name('booking.create')->middleware('can:add.booking');

        Route::post('nieuw', 'PlanningController@createBooking')
            ->middleware('can:add.booking');

        Route::get('edit/{booking}', function (Booking $booking) {
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
                'room_id' => $booking->rooms[0]->id,
                'part' => $booking->rooms[0]->properties->options['part'],
                'options' => $booking->rooms[0]->properties->options,
                'max_beds' => Room::getMaxBeds(),
            ]);
        })->name('booking.edit')->middleware('can:add.booking');

        Route::post('edit/{booking}', 'PlanningController@editBooking')
            ->middleware('can:add.booking');

        Route::get('{booking}', function (App\Booking $booking) {
            $countries = PlanningController::getCountryList('nl_BE');
            $guests = Guest::orderBy('lastname')->get();
            $booking->load(['rooms', 'customer']);
            return view('planning.show', [
                "booking" => $booking,
                'guests' => $guests,
                'countries' => $countries,
                'extras' => Extra::all()]);
        })->name('booking.show')->where('booking', '[0-9]+');

        Route::get('del/{booking}', function (App\Booking $booking) {
            $arrival = $booking->arrival;
            $departure = $booking->departure;
            $room = $booking->rooms[0];

            $booking->delete();

            event(new BookingDeletedEvent($room, $arrival, $departure));

            return redirect()->route('planning', ['date' => $arrival->toDateString()]);
        })->name('booking.delete');

        Route::get('getGuests', 'AjaxController@getGuests')
            ->name('booking.ajax.guests');

        Route::post('saveGuest', 'AjaxController@saveGuest')
            ->name('booking.ajax.guest.save');

        Route::get('search', 'PlanningController@search')
            ->name('booking.search');

        Route::post('addExtraGuest', 'AjaxController@addExtraGuest');
        Route::get('{booking}/del-extra-guest/{guest}', 'PlanningController@delExtraGuest')
            ->name('booking.extra.delete');

        Route::post('addExtra', 'ExtraController@addExtra');
        Route::get('{booking}/del-extra/{extra}', 'PlanningController@delExtra')
            ->name('booking.extras.delete');

        Route::get('export-emails', 'ExportController@exportEmails')->name('export.email');

        Route::get('print', 'PrintController@print')->name('print');
    });

    /**
     * GUESTS
     */
    Route::middleware(['can:edit.all'])->prefix('gast')->group(function () {

        Route::get('edit/{booking}/{guest}', function (App\Booking $booking, App\Guest $guest) {
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

    /**
     * ROOMS
     */
    Route::middleware('can:edit.all')->prefix('kamers')->group(function () {
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
            $layout = array_map('intval', explode(',', $request->input('layout')));

            $r = new Room;
            $r->name = $name;
            $r->beds = $beds;
            $r->layout = $layout;
            $r->save();

            return redirect()->route('rooms');
        });

        Route::get('edit/{room}', function(App\Room $room) {
            return view('rooms.add', ['room' => $room]);
        })->name('room.edit');

        Route::post('edit/{room}', function(App\Room $room, Request $request) {
            $name = $request->input('name');
            $beds = (int)$request->input('beds');
            $layout = array_map('intval', explode(',', $request->input('layout')));

            $room->name = $name;
            $room->beds = $beds;
            $room->layout = $layout;
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

    /**
     * EXTRAS
     */
    Route::middleware('can:edit.all')->prefix('extras')->group(function () {
        Route::get('/', 'ExtraController@index')->name('extra');

        Route::get('new', function () {
            return view('extra.create');
        })->name('extra.create');

        Route::post('new', 'ExtraController@createExtra');

        Route::get('edit/{extra}', function (Extra $extra) {
            return view('extra.create', ['extra' => $extra]);
        })->name('extra.edit');

        Route::post('edit/{extra}', 'ExtraController@editExtra');

        Route::get('del/{extra}', function (Extra $extra) {
            $extra->delete();

            return redirect()->route('extra');
        })->name('extra.delete');
    });

    /**
     * STATS
     */
    Route::middleware('can:edit.all')->prefix('stats')->group(function () {
        Route::get('/', 'StatsController@index')->name('stats');

        // Route::get('new', function () {
        //     return view('extra.create');
        // })->name('extra.create');

        // Route::post('new', 'ExtraController@createExtra');

        // Route::get('edit/{extra}', function (Extra $extra) {
        //     return view('extra.create', ['extra' => $extra]);
        // })->name('extra.edit');

        // Route::post('edit/{extra}', 'ExtraController@editExtra');

        // Route::get('del/{extra}', function (Extra $extra) {
        //     $extra->delete();

        //     return redirect()->route('extra');
        // })->name('extra.delete');
    });

    /**
     * ADMIN
     */
    Route::middleware('can:access.admin')->prefix('admin')->group(function () {
        Route::get('/', function () {
            $rooms = Room::orderBy('sorting')->get();

            $users = User::all();

            return view('admin.index', ['user' => Auth::user(), 'users' => $users]);
        })->name('admin')->middleware('can:access.admin');

        Route::post('passwd', 'Auth\ChangePasswordController@ChangePassword')->name('passwd');

        Route::prefix('user')->group(function () {
            Route::get('create', function () {
                return view('admin.user_create', ['roles' => Role::all()]);
            })->name('user.create')->middleware('can:edit.users');

            Route::post('create', 'UserController@createUser')->middleware('can:edit.users');

            Route::get('update/{user}', function (User $user) {
                return view('admin.user_create', ['roles' => Role::all(), 'user' => $user, 'update_me' => false]);
            })->name('user.update')->middleware('can:edit.users');

            Route::post('update/{user}', 'UserController@updateUser')->middleware('can:edit.users');

            Route::get('update-me', function () {
                return view('admin.user_create', ['roles' => Role::all(), 'user' => Auth::user(), 'update_me' => true]);
            })->name('profile.update');

            Route::post('update-me', 'UserController@updateMe');

            Route::get('del/{user}', function (User $user) {
                $user->delete();
                return redirect()
                    ->route('admin', ['#users'])
                    ->with('success', 'User deleted sucessfully');
            })->name('user.del');
        });
    });

    /**
     * WEEKLY NOTES
     */
    Route::middleware(['can:edit.all'])->prefix('notes')->group(function () {
        Route::post('save', 'AjaxController@saveNote');
    });
});
