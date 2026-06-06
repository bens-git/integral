<?php

namespace App\Models\Cds;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliberationThread extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'issue_id',
        'parent_id',
        'created_by_id',
        'title',
        'topic',
        'status',
        'is_pinned',
        'is_locked',
        'message_count',
        'last_activity_at',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_locked' => 'boolean',
        'message_count' => 'integer',
        'last_activity_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    public function issue(): BelongsTo
    {
        return $this->belongsTo(DecisionIssue::class, 'issue_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(DeliberationThread::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(DeliberationThread::class, 'parent_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'created_by_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(DeliberationMessage::class, 'thread_id');
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    public function isArchived(): bool
    {
        return $this->status === 'archived';
    }

    public function isPinned(): bool
    {
        return $this->is_pinned;
    }

    public function isLocked(): bool
    {
        return $this->is_locked;
    }

    public function incrementMessageCount(): void
    {
        $this->increment('message_count');
        $this->update(['last_activity_at' => now()]);
    }
}