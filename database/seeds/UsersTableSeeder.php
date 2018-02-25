<?php

use Illuminate\Database\Seeder;

use App\User;
use App\Role;

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
        $user->role()->associate(Role::where('name', 'viewer')->first());
        $user->save();

        $user = new User();
        $user->username = "janedoe";
        $user->name = "Jane Doe";
        $user->email = "janedoe@example.com";
        $user->password = Hash::make("secret");
        $user->role()->associate(Role::where('name', 'admin')->first());
        $user->save();
    }
}
