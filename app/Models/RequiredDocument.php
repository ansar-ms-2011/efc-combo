<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class RequiredDocument extends Model
{
    protected $fillable = [
        'key',
        'name',
        'urdu_name',
        'service_name',
        'service_type',
        'required_copy',
        'file_type',
        'reason_type_id',
        'active',
        'max_size_in_mb',
        'max_size_in_bytes',
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    protected $appends = ['removed_from_frontend'];

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public static function booted()
    {

        static::created(function () {
            // Clear cache when a new document is created
            Cache::forget('grouped-types');
        });
        static::updated(function () {
            // Clear cache when a document is updated
            Cache::forget('grouped-types');
        });
        static::deleted(function () {
            // Clear cache when a document is deleted
            Cache::forget('grouped-types');
        });
    }

    public function reasonType(): BelongsTo
    {
        return $this->belongsTo(Type::class, 'reason_type_id');
    }

    public function getRemovedFromFrontendAttribute()
    {
        return false;
    }

}
