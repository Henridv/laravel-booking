<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
			$guest = $request->input('guest');

			$g = new Guest;
			$g->firstname = $guest['firstname'];
			$g->lastname = $guest['lastname'];
			$g->email = $guest['email'];
			$g->phone = $guest['phone'];
			$g->country = $guest['country'];
			$g->save();

			$guest['id'] = $g->id;
			
			return json_encode($guest);
		} else {
			return Redirect::to('/');
		}
	}
}
