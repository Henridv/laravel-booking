<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User();
        $user->username = "johndoe";
        $user->name = "John Doe";
        $user->email = "johndoe@example.com";
        $user->password = Hash::make("secret");
        $user->save();
    }
}
