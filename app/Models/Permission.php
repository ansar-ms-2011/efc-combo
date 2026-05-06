<?php

namespace App\Models;

use spatie\Permission\Models\Permission as spatiePermission;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;
    protected $guarded=[];

    protected $table = 'permissions';

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function rolePermission()
    {
        return $this->hasMany(RolePermission::class);
    }

    //
}
