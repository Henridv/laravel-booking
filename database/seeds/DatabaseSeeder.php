<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RoomsTableSeeder::class);
        $this->call(GuestsTableSeeder::class);
        $this->call(BookingsTableSeeder::class);
    }
}
