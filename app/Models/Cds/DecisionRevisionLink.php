<?php

namespace App\Models\Cds;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DecisionRevisionLink extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'prior_decision_id',
        'revised_decision_id',
        'participant_id',
        'revision_type',
        'reason',
        'change_summary',
        'is_major_revision',
        'approved_by_id',
        'approved_at',
    ];

    protected $casts = [
        'is_major_revision' => 'boolean',
        'approved_at' => 'datetime',
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

    public function priorDecision(): BelongsTo
    {
        return $this->belongsTo(DecisionLedger::class, 'prior_decision_id');
    }

    public function revisedDecision(): BelongsTo
    {
        return $this->belongsTo(DecisionLedger::class, 'revised_decision_id');
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'participant_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'approved_by_id');
    }

    public function isAmendment(): bool
    {
        return $this->revision_type === 'amendment';
    }

    public function isCorrection(): bool
    {
        return $this->revision_type === 'correction';
    }

    public function isOverride(): bool
    {
        return $this->revision_type === 'override';
    }

    public function isReversal(): bool
    {
        return $this->revision_type === 'reversal';
    }

    public function isRefinement(): bool
    {
        return $this->revision_type === 'refinement';
    }

    public function isMajor(): bool
    {
        return $this->is_major_revision;
    }
}