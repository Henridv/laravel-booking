<?php

use App\Guest as Guest;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GuestsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i=0; $i < 100; $i++) {
            $guest = new Guest;
            $guest->firstname = "John";
            $guest->lastname = "Doe ".Str::random(2);
            $guest->email = Str::random(10)."@gmail.com";
            $guest->phone = Str::random(10);
            $guest->country = "BE";

            $hash = sha1($guest->name);

            $r = substr($hash, 0,2);
            $g = substr($hash, 2,2);
            $b = substr($hash, 4,2);

            $guest->color = '#'.$r.$g.$b;

            $guest->save();
        }
    }
}
