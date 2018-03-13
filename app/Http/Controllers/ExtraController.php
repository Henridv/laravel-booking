<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Extra;

class ExtraController extends Controller
{
    public function index()
    {
        return view('extra.index', ['extras' => Extra::all()]);
    }
}
