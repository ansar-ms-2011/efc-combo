<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model

{

    protected $guarded = [];



    public function department()
    {
        return $this->belongsTo(Department::class, 'dept_id');
    }

    public function status()
    {
        return $this->belongsTo(Type::class, 'status');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }


    public function centers()
    {
        return $this->belongsToMany(
            Center::class,
            'service_centers',
            'service_id',
            'center_id'
        );
    }

    public function instructions()
{
    return $this->hasMany(ServiceInstruction::class);
}

 public function serviceCenters()
    {
        return $this->hasMany(ServiceCenter::class);
    }

}
