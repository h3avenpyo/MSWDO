<?php

namespace App\Models\Wcpu;

use Spatie\Activitylog\Models\Activity as BaseActivity;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends BaseActivity
{
    /**
     * The database connection name for WCPU models.
     *
     * @var string
     */
    protected $connection = 'wcpu';

    /**
     * Get the user that triggered the activity.
     */
    public function causer(): MorphTo
    {
        if (config('activitylog.subject_returns_soft_deleted_models')) {
            return $this->morphTo()->withTrashed();
        }

        return $this->morphTo();
    }

    /**
     * Get the subject of the activity.
     */
    public function subject(): MorphTo
    {
        if (config('activitylog.subject_returns_soft_deleted_models')) {
            return $this->morphTo()->withTrashed();
        }

        return $this->morphTo();
    }



    /**
     * Get the formatted description of the activity.
     *
     * @return string
     */
    public function getDescriptionAttribute($value): ?string
    {
        return $value === null ? null : (string) $value;
    }

    /**
     * Get the formatted created at timestamp.
     *
     * @return string
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get the icon for the activity type.
     *
     * @return string
     */
    public function getIconAttribute(): string
    {
        switch ($this->event) {
            case 'created':
                return 'plus-circle';
            case 'updated':
                return 'pencil-alt';
            case 'deleted':
                return 'trash-alt';
            case 'restored':
                return 'trash-restore';
            default:
                return 'info-circle';
        }
    }

    /**
     * Get the CSS class for the activity type.
     *
     * @return string
     */
    public function getCssClassAttribute(): string
    {
        switch ($this->event) {
            case 'created':
                return 'success';
            case 'updated':
                return 'info';
            case 'deleted':
                return 'danger';
            case 'restored':
                return 'warning';
            default:
                return 'secondary';
        }
    }
}
