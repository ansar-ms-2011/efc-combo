<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CenterWorkingDay extends Model
{
    protected $table = 'center_working_days';
    protected $fillable = ['center_id', 'working_day_id'];

    // Types table se name lene ke liye
    public function type()
    {
        return $this->belongsTo(Type::class, 'working_day_id');
    }
}