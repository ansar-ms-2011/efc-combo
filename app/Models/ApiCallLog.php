<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiCallLog extends Model
{
    protected $updated_at = false;
    protected $fillable = [
        'endpoint',
        'method',
        'user_id',
        'ip',
        'status_code',
        'response_time_ms',
    ];
}
