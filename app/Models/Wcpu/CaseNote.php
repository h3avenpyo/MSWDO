<?php

namespace App\Models\Wcpu;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaseNote extends WcpuBaseModel
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'case_id',
        'user_id',
        'note',
        'is_important',
        'attachments',
        'parent_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_important' => 'boolean',
        'attachments' => 'array',
    ];

    /**
     * The case that this note belongs to.
     */
    public function case(): BelongsTo
    {
        return $this->belongsTo(CaseModel::class, 'case_id');
    }

    /**
     * The user who created this note.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The parent note if this is a reply.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(CaseNote::class, 'parent_id');
    }

    /**
     * Replies to this note.
     */
    public function replies(): HasMany
    {
        return $this->hasMany(CaseNote::class, 'parent_id');
    }

    /**
     * Scope a query to only include important notes.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeImportant($query)
    {
        return $query->where('is_important', true);
    }

    /**
     * Scope a query to only include root notes (not replies).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Check if the note has attachments.
     *
     * @return bool
     */
    public function hasAttachments(): bool
    {
        return !empty($this->attachments);
    }

    /**
     * Get the number of attachments.
     *
     * @return int
     */
    public function getAttachmentsCountAttribute(): int
    {
        return $this->hasAttachments() ? count($this->attachments) : 0;
    }
}
