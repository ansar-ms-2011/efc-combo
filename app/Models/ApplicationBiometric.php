<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class ApplicationBiometric extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'application_id',
        'applicant_id',
        'finger_type',
        'image_path',
        'feature_set',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected function imagePath(): Attribute
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
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }
    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }
}
