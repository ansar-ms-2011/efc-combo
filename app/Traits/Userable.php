<?php

namespace App\Traits;

use App\Models\User;

trait Userable
{
    /**
     * Relationship: Who created the model
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship: Who updated the model
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Relationship: Who deleted the model
     */
    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Boot the trait to automatically fill created_by and updated_by
     */
    protected static function bootBlameable()
    {
        static::creating(function ($model) {
            if (auth()->check()) {
                $model->created_by = auth()->id();
                $model->updated_by = auth()->id();
            }
        });

        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->id();
            }
        });

        static::deleting(function ($model) {
            if (auth()->check() && $model->isSoftDeleteEnabled()) {
                $model->deleted_by = auth()->id();
                $model->save();
            }
        });
    }

    /**
     * Helper to check if the model uses soft deletes
     */
    protected function isSoftDeleteEnabled(): bool
    {
        return in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($this));
    }
}
