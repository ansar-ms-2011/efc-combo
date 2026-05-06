<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $fillable = [
        'name',
        'content',
        'user_id',
    ];

    // Relation (template belongs to user)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
