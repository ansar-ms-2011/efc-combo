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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Application extends Model
{
    use HasFactory, SoftDeletes, Userable;

    public const DESK_DEO = 'DEO';
    public const DESK_AC = 'AC';
    public const DESK_ACR = 'ACR';
    public const DESK_DC = 'DC';

    protected $table = 'applications';

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'uuid',
        'tracking_token_no',
        'certificate_type',
        'applicant_id',
        'center_id',
        'current_status',
        'lifecycle_status',
        'application_type_id',
        'application_for_id',
        'missal_no',
        'entry_datetime',
        'amount',
        'on_desk',
        'guardian_type_id',
        'delivery_mode',
        'tehsil_id',
        'district_id',
        'region_id',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
        'created_at',
        'updated_at',
        'source'
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'entry_datetime' => 'datetime:Y-m-d',
        'amount' => 'decimal:2',
    ];

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
                return url(Storage::url($value));
            }
        );
    }

    protected function qrCodeUrl(): Attribute
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

    protected static function booted()
    {
        static::creating(function ($application) {
            if (empty($application->uuid)) {
                $application->uuid = (string)Str::uuid();
            }

            if (empty($application->tracking_token_no)) {
                DB::transaction(function () use ($application) {
                    $demographics = Demography::whereIn('id', [
                        $application->region_id,
                        $application->district_id,
                        $application->tehsil_id
                    ])->get()->keyBy('id');

                    $regionCode = $demographics[$application->region_id]->code ?? '';
                    $districtCode = $demographics[$application->district_id]->code ?? '';
                    $tehsilCode = $demographics[$application->tehsil_id]->code ?? '';

                    $locationKey = "A{$regionCode}{$districtCode}{$tehsilCode}";

                    // Get or create sequence for this location
                    $sequence = TokenSequence::where('location_key', $locationKey)
                        ->lockForUpdate()
                        ->first();

                    if (!$sequence) {
                        $sequence = TokenSequence::create([
                            'location_key' => $locationKey,
                            'last_number' => 0
                        ]);
                    }

                    $nextNumber = $sequence->last_number + 1;
                    $formattedNumber = str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
                    $application->tracking_token_no = $locationKey . $formattedNumber;

                    // Update sequence
                    $sequence->update(['last_number' => $nextNumber]);
                });
            }
        });
    }

    public function center(): BelongsTo
    {
        return $this->belongsTo(Center::class);
    }

    public function guardianType(): BelongsTo
    {
        return $this->belongsTo(Type::class, 'guardian_type_id');
    }

    public function applicationType(): BelongsTo
    {
        return $this->belongsTo(Type::class, 'application_type_id');
    }

    public function applicationFor(): BelongsTo
    {
        return $this->belongsTo(Type::class, 'application_for_id');
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    public function approvals(): HasMany|Application
    {
        return $this->hasMany(ApprovalDetail::class);
    }

    public function appointment(): HasOne|Application
    {
        return $this->hasOne(Appointment::class);
    }

    public function certificates(): HasMany|Application
    {
        return $this->hasMany(ApplicationCertificate::class);
    }

    public function biometrics(): HasMany|Application
    {
        return $this->hasMany(ApplicationBiometric::class);
    }

    public function documents(): HasMany|Application
    {
        return $this->hasMany(ApplicationDocument::class);
    }

    public function workFlows(): HasMany|Application
    {
        return $this->hasMany(WorkflowTransition::class, 'application_id');
    }

    public function duplicateDetails(): HasOne|Application
    {
        return $this->hasOne(DuplicateDetail::class);
    }

    public function deliveryDetails(): HasOne|Application
    {
        return $this->hasOne(DeliveryDetail::class);
    }
}
