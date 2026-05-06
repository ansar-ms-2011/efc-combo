<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkflowTransition extends Model
{
    protected $fillable = [
        'application_id',
        'from_status',
        'to_status',
        'created_by',
        'action',
        'remarks',
        'created_by',
        'created_at'
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
