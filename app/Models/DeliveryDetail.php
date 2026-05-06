<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryDetail extends Model
{
    protected $fillable = [
        'user_id',
        'application_id',
        'delivery_mode',
        'delivery_address',
        'delivery_phone',
        'dispatch_date',
        'collected_by',
        'collection_date',
    ];

    protected $casts = [
        'delivery_date' => 'date',
        'dispatch_date' => 'date',
        'collection_date' => 'date',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot(){
        parent::boot();
        static::creating(function ($model) {
            $model->user_id = auth()->id();
        });
    }

}
