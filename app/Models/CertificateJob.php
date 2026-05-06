<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CertificateJob extends Model
{
    protected $fillable=[
        'id',
        'application_id',
        'type',
        'status',
        'started_at',
        'completed_at',
        'message',
        're_initiated',
        're_initiated_at',
        're_initiated_by',
    ];
    protected $casts=[
        'processing_time'=>'datetime',
    ];
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }
}
