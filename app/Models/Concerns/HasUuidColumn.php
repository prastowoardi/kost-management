<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

trait HasUuidColumn
{
    protected static function bootHasUuidColumn(): void
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
