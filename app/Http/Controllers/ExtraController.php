<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Booking;
use App\Extra;

class ExtraController extends Controller
{
    public function index()
    {
        return view('extra.index', ['extras' => Extra::all()]);
    }

    public function saveExtra(Request $request, Extra $extra)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric|min:0',
            'per' => 'required|string',
            'icon' => 'string'
        ]);

        $extra->name = $request->input('name');
        $extra->price = $request->input('price');
        $extra->per = $request->input('per');
        $extra->fa_icon = $request->input('icon');

        $extra->save();

        return true;
    }

    public function editExtra(Request $request, Extra $extra)
    {
        $result = $this->saveExtra($request, $extra);

        if ($result) {
            return redirect()
                ->route('extra')
                ->with('success', 'Extra succesvol gewijzigd');
        } else {
            return redirect()
                ->route('extra.edit', ['extra' => $extra])
                ->with('error', 'Fout bij het wijzigen')
                ->withInput();
        }
    }

    public function createExtra(Request $request)
    {
        $extra = new Extra;
        $result = $this->saveExtra($request, $extra);

        if ($result) {
            return redirect()
                ->route('extra')
                ->with('success', 'Extra toegevoegd');
        } else {
            return redirect()
                ->route('extra.create')
                ->with('error', 'Fout bij het toevoegen')
                ->withInput();
        }
    }

    public function addExtra(Request $request)
    {
        if (!$request->ajax()) {
            return redirect()->to('/');
        }

        $booking_id = (int)$request->input('booking');
        $booking = Booking::findOrFail($booking_id);

        $extra_id = (int)$request->input('extra');
        $extra = Extra::findorFail($extra_id);

        $amount = (int)$request->input('amount');

        $booking->extras()->attach($extra_id, ['amount' => $amount]);

        return json_encode(['ok']);
    }
}
