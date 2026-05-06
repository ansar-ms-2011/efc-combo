<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Employee extends Model
{
    use HasFactory;

     protected $fillable = [
        'user_id',
        'cnic',
        'phone_no',
        'address',
        'center_id',
        'designation_id',
        'created_by',
        'updated_by',
    ];

    // Relationship
    public function center(){
        return $this->belongsTo(Center::class, 'center_id');
    }

    public function designation()
{
    return $this->belongsTo(Role::class, 'designation_id');
}

public function user()
{
    return $this->belongsTo(User::class);
}

public function getProfilePhotoUrlAttribute()
{
    return $this->profile_photo
        ? asset('storage/' . $this->profile_photo)
        : null;
}
}
