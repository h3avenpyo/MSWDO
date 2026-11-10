<?php

namespace App\Models\Builders;

use App\Services\MswdoActivityLogger;
use Illuminate\Database\Eloquent\Builder;

/** Capture instance and bulk writes once, including writes that bypass model events. */
class MswdoAuditedBuilder extends Builder
{
    public function update(array $values)
    {
        $logger = app(MswdoActivityLogger::class);
        if (! $actor = $logger->actor()) {
            return parent::update($values);
        }

        return $this->getModel()->getConnection()->transaction(function () use ($values, $logger, $actor) {
            $before = (clone $this)->lockForUpdate()->get();
            $affected = parent::update($values);
            if ($affected && $before->isNotEmpty()) {
                $after = $this->getModel()->newQueryWithoutScopes()->whereKey($before->modelKeys())->get()->keyBy($this->getModel()->getKeyName());
                foreach ($before as $model) {
                    if ($current = $after->get($model->getKey())) {
                        $logger->change($current, 'updated', $model->getAttributes(), $current->getAttributes(), $actor);
                    }
                }
            }

            return $affected;
        });
    }

    public function delete()
    {
        $logger = app(MswdoActivityLogger::class);
        if (! $actor = $logger->actor()) {
            return parent::delete();
        }

        return $this->getModel()->getConnection()->transaction(function () use ($logger, $actor) {
            $before = (clone $this)->lockForUpdate()->get();
            $affected = parent::delete();
            if ($affected) {
                foreach ($before as $model) {
                    $logger->change($model, 'deleted', $model->getAttributes(), [], $actor);
                }
            }

            return $affected;
        });
    }
}
