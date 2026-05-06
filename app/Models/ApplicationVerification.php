<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationVerification extends Model
{
    protected $fillable = [
         'application_certificate_id',
    'status',
    'verified_by',
    'img_upload_by',
    'data_enter_by',
    'verified_at',
    'remarks'
    ];

    // Verification belongs to a Certificate
    public function certificate()
    {
        return $this->belongsTo(ApplicationCertificate::class, 'application_certificate_id');
    }

    // Verification belongs to a User 
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
     public function dataEnterer()
    {
        return $this->belongsTo(User::class, 'data_enter_by');
    }

    // ✅ yeh add karo
    public function imageUploader()
    {
        return $this->belongsTo(User::class, 'img_upload_by');
    }
}
