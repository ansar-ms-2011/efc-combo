<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefugeeDetail extends Model
{
    use HasFactory;

    protected $table = 'refugee_details';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'applicant_id',
        'refugee_number',
        'refugee_from',
        'refugee_year',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'refugee_year' => 'integer',
    ];

    /**
     * Relationships
     */
    public function applicant()
    {
        return $this->belongsTo(Applicant::class, 'applicant_id');
    }
}
