<?php

namespace App\Models\Cds;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DecisionDispatch extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'consensus_id',
        'issue_id',
        'node_id',
        'target_system',
        'action_type',
        'payload_summary',
        'status',
        'priority',
        'dispatched_by_id',
        'completed_by_id',
        'result_notes',
        'error_message',
        'retry_count',
        'dispatched_at',
        'completed_at',
        'next_retry_at',
    ];

    protected $casts = [
        'retry_count' => 'integer',
        'dispatched_at' => 'datetime',
        'completed_at' => 'datetime',
        'next_retry_at' => 'datetime',
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

    public function consensus(): BelongsTo
    {
        return $this->belongsTo(ConsensusModel::class, 'consensus_id');
    }

    public function issue(): BelongsTo
    {
        return $this->belongsTo(DecisionIssue::class, 'issue_id');
    }

    public function dispatchedBy(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'dispatched_by_id');
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'completed_by_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function shouldRetry(): bool
    {
        return $this->isFailed() && $this->retry_count < 3 && $this->next_retry_at?->isPast();
    }
}