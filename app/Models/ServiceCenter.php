<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceCenter extends Model
{

    protected $guarded = [];

    public function center()
{
    return $this->belongsTo(Center::class);
}

public function user()
{
    return $this->belongsTo(User::class);
}
public function users()
    {
        return $this->hasMany(ServiceCenterUser::class);
    }




    public function service() {
        return $this->belongsTo(Service::class);
    }

}
