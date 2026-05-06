<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

 // App\Models\Tehsil.php
class Tehsil extends Model
{
    protected $fillable = [
        'district_name',
        'tehsil_code',
        'tehsil_name',
        'tehsil_name_urdu',
        'created_by',
        'updated_by',
    ];
}


