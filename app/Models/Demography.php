<?php

namespace App\Models;

use Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Demography extends Model
{
    protected $guarded = [];

    public function parent()
    {
        return $this->belongsTo(Demography::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Demography::class, 'parent_id');
    }

    public function tehsils(): Demography|HasMany
    {
        return $this->hasMany(Demography::class, 'parent_id')->where('type', 'TEHSIL');
    }
    public function cities(): Demography|HasMany
    {
        return $this->hasMany(Demography::class, 'parent_id')->where('type', 'CITY');
    }
    public function unionCouncils(): Demography|HasMany
    {
        return $this->hasMany(Demography::class, 'parent_id')->where('type', 'UNION_COUNCIL');
    }

    public function districts(): Demography|HasMany
    {
        return $this->hasMany(Demography::class, 'parent_id')->where('type', 'DISTRICT');
    }

    public function district(){
        return $this->belongsTo(Demography::class, 'parent_id');
    }

    protected static function booted(){
        parent::booted();

        static::created(function ($demography) {
            Cache::forget('grouped-districts-and-tehsils');
            Cache::forget('users-dropdown-data');
        });
        static::updated(function ($demography) {
            Cache::forget('grouped-districts-and-tehsils');
            Cache::forget('users-dropdown-data');
        });
        static::deleted(function ($demography) {
            Cache::forget('grouped-districts-and-tehsils');
            Cache::forget('users-dropdown-data');
        });
    }

}
