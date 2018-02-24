<?php

use Illuminate\Database\Seeder;
use App\Guest as Guest;

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
            $guest->lastname = "Doe ".str_random(2);
            $guest->email = str_random(10)."@gmail.com";
            $guest->phone = str_random(10);
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
