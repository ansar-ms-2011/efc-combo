<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TokenSequence extends Model
{
    //
    protected $fillable = [
        'location_key',
        'last_number',
    ];
}
