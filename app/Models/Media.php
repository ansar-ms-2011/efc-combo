<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    //
    // 


protected $appends = ['url'];

public function getUrlAttribute()
{
    return asset('storage/' . $this->file_path);
}
public function mediable()
{
    return $this->morphTo();    
}
}