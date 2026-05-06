<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DuplicateDetail extends Model
{
    protected $fillable = [
        'application_id',
        'reason',
        'reason_type_id',
        'duplicate_of_id',
        'attached_doc_path',
        'guardian_name',
        'guardian_cnic',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }
    public function duplicateOf(): BelongsTo
    {
        return $this->belongsTo(ApplicationCertificate::class,'duplicate_of_id');
    }
}
