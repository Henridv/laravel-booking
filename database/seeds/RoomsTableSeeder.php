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
            $r->name = str_random(10);
            $r->beds = rand(1,6);
            $r->save();
        }
    }
}
