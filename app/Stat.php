<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stat extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'datetime_start', 'datetime_end', 'duration'
    ];
}
