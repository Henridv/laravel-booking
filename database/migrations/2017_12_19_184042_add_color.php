<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Guest;

class AddColor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->string('color')->default("#FFFFFF");
        });

        $guests = Guest::all();
        foreach ($guests as $guest) {

            $name = $guest->name;
            $hash = sha1($name);

            $r = substr($hash, 0,2);
            $g = substr($hash, 2,2);
            $b = substr($hash, 4,2);
            
            $guest->color = '#'.$r.$g.$b;
            $guest->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }
}
