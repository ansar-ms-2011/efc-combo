<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceCenterUser extends Model
{

    protected $guarded = [];

    public function serviceCenter()

    {
        return $this->belongsTo(ServiceCenter::class, 'service_center_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
