<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class ApplicationDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'application_id',
        'required_document_id',
        'upload_method',
        'file_path',
        'mime_type',
        'original_name',
        'ac_acr_verified',
        'ac_acr_verified_date',
        'dc_verified',
        'dc_verified_date',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'ac_acr_verified' => 'boolean',
        'dc_verified' => 'boolean',
        'ac_acr_verified_date' => 'date',
        'dc_verified_date' => 'date',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    protected function filePath(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (empty($value)) {
                    return null;
                }

                // Check if it's already a full URL
                if (filter_var($value, FILTER_VALIDATE_URL)) {
                    return $value;
                }

                // Return storage URL
                return url(Storage::url($value));
            }
        );
    }
}
