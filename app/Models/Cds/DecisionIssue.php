<?php

namespace App\Models\Cds;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DecisionIssue extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'submission_id',
        'node_id',
        'framed_problem',
        'scope',
        'success_criteria',
        'constraints',
        'priority',
        'status',
        'decision_type',
        'facilitator_id',
        'framing_completed_at',
        'deliberation_started_at',
        'consensus_reached_at',
        'implemented_at',
    ];

    protected $casts = [
        'priority' => 'integer',
        'framing_completed_at' => 'datetime',
        'deliberation_started_at' => 'datetime',
        'consensus_reached_at' => 'datetime',
        'implemented_at' => 'datetime',
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

    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Submission::class, 'submission_id');
    }

    public function facilitator(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'facilitator_id');
    }

    public function scenarios(): HasMany
    {
        return $this->hasMany(Scenario::class, 'issue_id');
    }

    public function knowledgeMappings(): HasMany
    {
        return $this->hasMany(IssueKnowledgeMap::class, 'issue_id');
    }

    public function deliberationThreads(): HasMany
    {
        return $this->hasMany(DeliberationThread::class, 'issue_id');
    }

    public function consensusModels(): HasMany
    {
        return $this->hasMany(ConsensusModel::class, 'issue_id');
    }

    public function decisionLedger(): HasMany
    {
        return $this->hasMany(DecisionLedger::class, 'issue_id');
    }

    public function decisionDispatches(): HasMany
    {
        return $this->hasMany(DecisionDispatch::class, 'issue_id');
    }

    public function feedbackEvents(): HasMany
    {
        return $this->hasMany(FeedbackEvent::class, 'issue_id');
    }

    public function systemSignals(): HasMany
    {
        return $this->hasMany(SystemSignal::class, 'issue_id');
    }

    public function latestConsensus(): BelongsTo
    {
        return $this->belongsTo(ConsensusModel::class, 'id', 'issue_id')
            ->latestOfMany();
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isFraming(): bool
    {
        return $this->status === 'framing';
    }

    public function isDeliberation(): bool
    {
        return $this->status === 'deliberation';
    }

    public function isConsensus(): bool
    {
        return $this->status === 'consensus';
    }

    public function isDecided(): bool
    {
        return $this->status === 'decided';
    }

    public function isImplemented(): bool
    {
        return $this->status === 'implemented';
    }

    public function isArchived(): bool
    {
        return $this->status === 'archived';
    }
}