<?php

namespace App\Models;

use App\Traits\Userable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\{Storage};

class Applicant extends Model
{
    use SoftDeletes, Userable, HasFactory;


    /**
     * The attributes that are mass-assignable.
     */
   protected $fillable = [
        'uuid',
        'full_name',
        'identity_number',
        'identity_type',
        'dob',
        'pob',
        'identity_symbol',
        'father_name',
        'father_identity_number',
        'email',
        'phone',
        'occupation',
        'wife_husband_name',
        'guardian_type_id',
        'state_subject_class',
        'residence_place',
        'address',
        'address2',
        'address3',
        'address4',
        'region_id',
        'district_id',
        'tehsil_id',
        'religion_id',
        'gender_id',
        'marital_status_id',
        'location',
        'personal_image',
        'status',
        'created_by',
        'updated_by',
        'deleted_by'
 ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'dob' => 'date:Y-m-d',
        'status' => 'integer',
    ];

    /**
     * Boot function to auto-generate UUID.
     */
    protected static function booted()
    {
        static::creating(function ($applicant) {
            if (empty($applicant->uuid)) {
                $applicant->uuid = (string) Str::uuid();
            }
        });
    }

    protected function personalImage(): Attribute
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
                return Storage::url($value);
            }
        );
    }

    public function religion(): BelongsTo
    {
        return $this->belongsTo(Type::class, 'religion_id');
    }

    public function gender(): BelongsTo
    {
        return $this->belongsTo(Type::class, 'gender_id');
    }

    public function tehsil(): BelongsTo
    {
        return $this->belongsTo(Demography::class, 'tehsil_id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(Demography::class, 'district_id');
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Demography::class, 'region_id');
    }

    public function certificates(): HasMany|Application
    {
        return $this->hasMany(ApplicationCertificate::class);
    }

    public function maritalStatus(): BelongsTo
    {
        return $this->belongsTo(Type::class, 'marital_status_id');
    }
    public function guardianType(): BelongsTo
    {
        return $this->belongsTo(Type::class, 'guardian_type_id');
    }
    public function applications(): HasMany|Applicant
    {
        return $this->hasMany(Application::class);
    }
    public function refugeeDetails(): HasOne|Applicant
    {
        return $this->hasOne(RefugeeDetail::class, 'applicant_id');
    }

    public function children()
    {
        return $this->hasMany(ApplicantChild::class);
    }
    public function biometrics()
{
    return $this->hasMany(ApplicationBiometric::class, 'applicant_id');
}
}
