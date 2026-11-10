<?php

namespace App\Models\Concerns;

use App\Models\Builders\MswdoAuditedBuilder;
use App\Services\MswdoActivityLogger;

trait LogsMswdoActivity
{
    protected static function bootLogsMswdoActivity(): void
    {
        static::created(function ($model) {
            app(MswdoActivityLogger::class)->change($model, 'created', [], $model->getAttributes());
        });
    }

    public function newEloquentBuilder($query): MswdoAuditedBuilder
    {
        return new MswdoAuditedBuilder($query);
    }
}
