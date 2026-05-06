<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdditionalCharge extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    public function primaryUser()
    {
        return $this->belongsTo(User::class, 'primary_user_id');
    }

    public function temporaryUser()
    {
        return $this->belongsTo(User::class, 'temporary_user_id');
    }
}
