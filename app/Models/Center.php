<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Center extends Model
{
    // Relationship

    public function users()
    {
        return $this->hasMany(User::class, 'center_id', 'id');
    }

    protected $guarded = [];

    // public function workingDays()
    // {
    //     return $this->hasMany(CenterWorkingDay::class ,'center_id');
    // }

    public function workingDays()
{
    return $this->belongsToMany(Type::class, 'center_working_days', 'center_id', 'working_day_id');
}

    public function services()
    {
        return $this->belongsToMany(
            Service::class,
            'service_centers',
            'center_id',
            'service_id'
        );
    }

    protected static function booted(){
        parent::booted();

        static::created(function ($demography) {
            Cache::forget('users-dropdown-data');
        });
        static::updated(function ($demography) {
            Cache::forget('users-dropdown-data');
        });
        static::deleted(function ($demography) {
            Cache::forget('users-dropdown-data');
        });
    }
    public function district() {
    return $this->belongsTo(Demography::class, 'district_id');
}

public function tehsil() {
    return $this->belongsTo(Demography::class, 'tehsil_id');
}


}
