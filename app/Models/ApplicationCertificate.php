<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ApplicationCertificate extends Model
{
    use HasFactory;

    protected $table = 'application_certificates';

    protected $fillable = [
        'uuid',
        'applicant_id',
        'application_id',
        'type',
        'source',
        'certificate_number',
        'issue_date',
        'pdf_path',
        'misal_no',
        'preview_path',
        'is_revoked',
        'uploaded_by',
        'status'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'is_revoked' => 'boolean',
    ];

    protected $appends = ['url', 'preview_url'];

    /**
     * Relationships
     */

    public function applicant()
    {
        return $this->belongsTo(Applicant::class, 'applicant_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function verification()
    {
        return $this->hasOne(ApplicationVerification::class, 'application_certificate_id');
    }

    public function application()
    {
        return $this->belongsTo(Application::class, 'application_id');
    }

    /**
     * Accessors
     */
    public function getUrlAttribute()
    {
        return $this->pdf_path ? url('storage/' . $this->pdf_path) : null;
    }

    public function getPreviewUrlAttribute()
    {
        return $this->pdf_path ? url('storage/' . $this->pdf_path) : null;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });

        static::deleting(function ($certificate) {
            if ($certificate->verification) {
                $certificate->verification()->delete();
            }

            // 
            if ($certificate->pdf_path && $certificate->pdf_path !== 'pending') {
                if (Storage::disk('public')->exists($certificate->pdf_path)) {
                    Storage::disk('public')->delete($certificate->pdf_path);
                }
            }
        });
    }
}
