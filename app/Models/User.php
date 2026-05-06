<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */

    use HasApiTokens, HasFactory, Notifiable, HasRoles;
    // use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass-assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'prefix',
        'first_name',
        'last_name',
        'email',
        'password',
        'region_id',
        'district_id',
        'tehsil_id',
        'city_id',
        'center_id',
        'department_id',
        'is_active',
        'sign_file',
        'e_sign',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
        'e_sign',
    ];

    protected $casts = [
    'is_active' => 'boolean',
    'keyboard_settings' => 'array',
    'email_verified_at' => 'datetime',
    'password' => 'hashed',
];

    protected $appends = [
        'two_fa_enabled',
        'current_role',
        'sign_url',
        'image_url',
        'name',
    ];

    public function getTwoFaEnabledAttribute()
    {
        return isset($this->two_factor_secret) && !is_null($this->two_factor_secret)
            && isset($this->two_factor_confirmed_at) && !is_null($this->two_factor_confirmed_at);
    }

    public function getCurrentRoleAttribute()
    {
        return $this->roles()->select('id', 'name')->first();
    }

    public function getNameAttribute()
    {
        $prefix = $this->prefix ? $this->prefix . ' ' : '';
        $firstName = $this->first_name ? $this->first_name . ' ' : '';
        $lastName = $this->last_name ? $this->last_name . ' ' : '';
        return trim("{$prefix}{$firstName}{$lastName}");
    }

    public function getImageUrlAttribute()
    {
        return url(Storage::url($this->image));
    }

    public function employee()
    {
        return $this->hasOne(Employee::class, 'user_id');
    }

    public function center()
    {
        return $this->belongsTo(Center::class, 'center_id');
    }

    public function region()
    {
        return $this->belongsTo(Demography::class, 'region_id');
    }

    public function district()
    {
        return $this->belongsTo(Demography::class, 'district_id');
    }


    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function tehsil()
    {
        return $this->belongsTo(Demography::class, 'tehsil_id');
    }

    public function city()
    {
        return $this->belongsTo(Demography::class, 'city_id');
    }

    public function getSignUrlAttribute()
    {
        if ($this->sign_file) {
            return asset('storage/' . $this->sign_file);
        }
        return null;
    }

    public function serviceCenters(): BelongsToMany
    {
        return $this->belongsToMany(
            ServiceCenter::class,
            'service_center_users',
            'user_id',
            'service_center_id'
        );
    }
}
