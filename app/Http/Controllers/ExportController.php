<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Guest;

class ExportController extends Controller
{
    public function exportEmails(Request $request)
    {
        $start = Carbon::parse($request->query('date'))->startOfDay();
        $end = Carbon::parse($start)->addWeek();
        $filename = 'emails-'.$start->format('d-m-Y').'.csv';
        $path = storage_path('app/latest-export.csv');

        $guests = Guest::whereHas('bookings', function ($q) use ($start, $end) {
            $q->where('arrival', '<', $end)->where('departure', '>', $start);
        })->whereNotNull('email')->get()->unique();

        $fhandle = fopen($path, 'w+');
        fwrite($fhandle, 'name,email'."\n");
        foreach ($guests as $guest) {
            fwrite($fhandle, $guest->name.','.$guest->email."\n");
        }
        fclose($fhandle);

        $headers = ['Content-Type: text/csv'];

        return response()->download($path, $filename, $headers)->deleteFileAfterSend(true);
    }
}
