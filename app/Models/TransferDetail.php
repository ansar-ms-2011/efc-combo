<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransferDetail extends Model
{
    protected $fillable = [
        'user_id',
        'created_by',

        'from_region_id',
        'from_district_id',
        'from_tehsil_id',

        'to_region_id',
        'to_district_id',
        'to_tehsil_id',

        'center_id',
        'posting_letter',
    ];

    public function services()
{
    return $this->belongsToMany(
        Service::class,
        'transfer_detail_services', // pivot table
        'transfer_detail_id',
        'service_id'
    );
}

}
