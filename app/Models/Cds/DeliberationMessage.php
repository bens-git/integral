<?php

namespace App\Models\Cds;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliberationMessage extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'thread_id',
        'participant_id',
        'parent_id',
        'message',
        'stance',
        'message_type',
        'is_edited',
        'edited_at',
        'upvotes',
        'downvotes',
        'is_resolved',
        'resolved_by_id',
    ];

    protected $casts = [
        'is_edited' => 'boolean',
        'edited_at' => 'datetime',
        'upvotes' => 'integer',
        'downvotes' => 'integer',
        'is_resolved' => 'boolean',
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

    public function thread(): BelongsTo
    {
        return $this->belongsTo(DeliberationThread::class, 'thread_id');
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'participant_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(DeliberationMessage::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(DeliberationMessage::class, 'parent_id');
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'resolved_by_id');
    }

    public function isSupport(): bool
    {
        return $this->stance === 'support';
    }

    public function isConcern(): bool
    {
        return $this->stance === 'concern';
    }

    public function isObjection(): bool
    {
        return $this->stance === 'objection';
    }

    public function isQuestion(): bool
    {
        return $this->stance === 'question';
    }

    public function isSuggestion(): bool
    {
        return $this->stance === 'suggestion';
    }

    public function getScoreAttribute(): int
    {
        return $this->upvotes - $this->downvotes;
    }
}