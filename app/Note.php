<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class Note extends Model
{
    protected $table = 'weekly_notes';
    protected $fillable = ['week_start'];
}
