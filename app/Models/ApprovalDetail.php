<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Storage;

class ApprovalDetail extends Model
{
    use HasFactory;

    protected $table = 'approval_details';
    protected $appends = ['esign_url'];
    /**
     * The attributes that are mass-assignable.
     */
    protected $fillable = [
        'application_id',
        'officer_id',
        'officer_name',
        'designation',
        'esign',
        'sign_date',
        'level',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'sign_date' => 'datetime',
    ];

    /**
     * Relationships
     */

    public function application()
    {
        return $this->belongsTo(Application::class, 'application_id');
    }

    public function officer()
    {
        return $this->belongsTo(User::class, 'officer_id');
    }

    public function getEsignUrlAttribute(){
        return url(Storage::url($this->esign));
    }
}
