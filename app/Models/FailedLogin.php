<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FailedLogin extends Model
{
    protected $updated_at = false;

    protected $fillable = [
        'email',
        'ip',
        'user_agent',
    ];
}
