<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Booking;
use App\Guest;

class AjaxController extends Controller
{
	public function getGuests(Request $request)
	{
		if ($request->ajax()) {
			$search = $request->query('term', '');
			$sql_search = '%'.$search.'%';

			$guests = Guest::where('firstname', 'LIKE', $sql_search)
						->orwhere('lastname', 'LIKE', $sql_search)
						->orwhere(DB::raw('CONCAT(firstname, " ", lastname)'), 'LIKE', $sql_search)
						->orderBy('lastname')->get();

			$guests_json = [];
			$guests_json[] = ["id" => -1, "text" => "Nieuwe gast"];
			foreach ($guests as $guest) {
				$guests_json[] = ["id" => $guest->id, "text" => $guest->name];
			}

			return json_encode(["results" => $guests_json]);
		} else {
			return Redirect::to('/');
		}
	}

	public function saveGuest(Request $request)
	{
		if ($request->ajax()) {
			$data = $request->input('guest');

			$guest = new Guest;
			$guest->firstname = $data['firstname'];
			$guest->lastname = $data['lastname'];
			$guest->email = $data['email'];
			$guest->phone = $data['phone'];
			$guest->country = $data['country'];

			$name = $guest->firstname .' '. $guest->lastname;
			$hash = sha1($name);

			$r = substr($hash, 0,2);
			$g = substr($hash, 2,2);
			$b = substr($hash, 4,2);

			$guest->color = '#'.$r.$g.$b;
			$guest->save();

			$data['id'] = $guest->id;

			return json_encode($data);
		} else {
			return Redirect::to('/');
		}
	}

    public function addExtraGuest(Request $request)
    {
        if ($request->ajax()) {
            $booking_id = $request->input('booking');
            $data = $request->input('guest');

            $booking = Booking::find($booking_id);
            if (isset($data['id'])) {
                $guest = Guest::find($data['id']);
            } else {
                $guest = new Guest;
                $guest->firstname = $data['firstname'];
                $guest->lastname = $data['lastname'];
                $guest->email = $data['email'];
                $guest->phone = $data['phone'];
                $guest->country = $data['country'];

                $name = $guest->firstname .' '. $guest->lastname;
                $hash = sha1($name);

                $r = substr($hash, 0, 2);
                $g = substr($hash, 2, 2);
                $b = substr($hash, 4, 2);

                $guest->color = '#'.$r.$g.$b;
                $guest->save();
            }

            $booking->extraGuests()->save($guest);

            return json_encode($guest);
        } else {
            return Redirect::to('/');
        }
    }
}
