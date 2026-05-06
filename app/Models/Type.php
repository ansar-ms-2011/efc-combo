<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Type extends Model
{
    //
    use SoftDeletes;

    protected $guarded = [];

    public function parent()
    {
        return $this->belongsTo(Type::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Type::class, 'parent_id');
    }

    protected static function boot()
    {
        parent::boot();

        //Clear Cache for dashboard counts
        static::created(function ($model) {
            Cache::forget('grouped-types');
        });
        static::updated(function ($model) {
            Cache::forget('grouped-types');
        });
        static::deleted(function ($model) {
            Cache::forget('grouped-types');
        });
    }
}

