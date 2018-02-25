<?php

use Illuminate\Database\Seeder;
use App\Room as Room;

class RoomsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i=0; $i < 5; $i++) {
            $r = new Room;
            $r->name = "Kamer ".($i+1);
            $r->beds = rand(3,6);
            $r->layout = [$r->beds];
            $r->sorting = Room::count();
            $r->save();
        }
    }
}
