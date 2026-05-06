<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $table = 'appointments';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'application_id',
        'qmatic_token',
        'appointment_date',
        'appointment_time',
        'delivery_date',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'appointment_date' => 'date',
        // 'appointment_time' => 'time',
        'delivery_date' => 'date',
    ];

    /**
     * Relationships
     */
    public function application()
    {
        return $this->belongsTo(Application::class, 'application_id');
    }
}
