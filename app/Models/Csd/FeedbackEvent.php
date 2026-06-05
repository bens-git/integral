<?php

namespace App\Models\Csd;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedbackEvent extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'issue_id',
        'participant_id',
        'node_id',
        'source',
        'feedback_type',
        'title',
        'description',
        'impact_score',
        'urgency_score',
        'status',
        'triggers_review',
        'reviewed_by_id',
        'review_notes',
        'reviewed_at',
    ];

    protected $casts = [
        'impact_score' => 'decimal:2',
        'urgency_score' => 'decimal:2',
        'triggers_review' => 'boolean',
        'reviewed_at' => 'datetime',
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

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'participant_id');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'reviewed_by_id');
    }

    public function isNew(): bool
    {
        return $this->status === 'new';
    }

    public function isReviewing(): bool
    {
        return $this->status === 'reviewing';
    }

    public function isAddressed(): bool
    {
        return $this->status === 'addressed';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    public function isHighImpact(): bool
    {
        return $this->impact_score !== null && abs($this->impact_score) >= 70;
    }

    public function isUrgent(): bool
    {
        return $this->urgency_score !== null && $this->urgency_score >= 80;
    }
}