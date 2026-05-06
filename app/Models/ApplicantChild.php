<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicantChild extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'applicant_id',
        'application_id',
        'name',
        'age'
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }
    public function applicant(){
        return $this->belongsTo(Applicant::class);
    }
}
