<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait GeneratesId
{
    /**
     * Generate a random 24-character MongoDB-style ID.
     */
    public static function generateId(): string
    {
        return Str::random(24);
    }

    /**
     * Boot method để tự động gán ID khi tạo model mới.
     */
    protected static function bootGeneratesId()
    {
        static::creating(function ($model) {
            if (!$model->{$model->getKeyName()}) {
                $model->{$model->getKeyName()} = self::generateId();
            }
        });
    }
}
