<?php

namespace App\Models\Cds;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ConsensusModel extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'issue_id',
        'node_id',
        'method',
        'consensus_score',
        'outcome',
        'threshold',
        'total_participants',
        'total_votes',
        'votes_strong_support',
        'votes_support',
        'votes_neutral',
        'votes_concern',
        'votes_block',
        'blocking_objections',
        'summary',
        'rationale',
        'facilitator_id',
        'voting_started_at',
        'voting_ended_at',
        'outcome_declared_at',
    ];

    protected $casts = [
        'consensus_score' => 'decimal:2',
        'threshold' => 'integer',
        'total_participants' => 'integer',
        'total_votes' => 'integer',
        'votes_strong_support' => 'integer',
        'votes_support' => 'integer',
        'votes_neutral' => 'integer',
        'votes_concern' => 'integer',
        'votes_block' => 'integer',
        'blocking_objections' => 'integer',
        'voting_started_at' => 'datetime',
        'voting_ended_at' => 'datetime',
        'outcome_declared_at' => 'datetime',
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

    public function facilitator(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'facilitator_id');
    }

    public function objections(): HasMany
    {
        return $this->hasMany(Objection::class, 'consensus_id');
    }

    public function decisionDispatch(): HasOne
    {
        return $this->hasOne(DecisionDispatch::class, 'consensus_id');
    }

    public function isPending(): bool
    {
        return $this->outcome === 'pending';
    }

    public function isConsensusReached(): bool
    {
        return $this->outcome === 'consensus_reached';
    }

    public function isConsent(): bool
    {
        return $this->outcome === 'consent';
    }

    public function isBlocked(): bool
    {
        return $this->outcome === 'blocked';
    }

    public function isWithdrawn(): bool
    {
        return $this->outcome === 'withdrawn';
    }

    public function hasConsensus(): bool
    {
        return $this->consensus_score !== null && $this->consensus_score >= $this->threshold;
    }

    public function hasBlockingObjections(): bool
    {
        return $this->blocking_objections > 0;
    }

    public function calculateConsensusScore(): float
    {
        if ($this->total_votes === 0) {
            return 0;
        }

        $weights = [
            'strong_support' => 100,
            'support' => 80,
            'neutral' => 50,
            'concern' => 20,
            'block' => 0,
        ];

        $totalScore = (
            $this->votes_strong_support * $weights['strong_support'] +
            $this->votes_support * $weights['support'] +
            $this->votes_neutral * $weights['neutral'] +
            $this->votes_concern * $weights['concern'] +
            $this->votes_block * $weights['block']
        );

        return round($totalScore / $this->total_votes, 2);
    }
}