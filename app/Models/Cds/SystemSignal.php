<?php

namespace App\Models\Cds;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemSignal extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'node_id',
        'proposal_id',
        'issue_id',
        'source',
        'signal_type',
        'severity',
        'title',
        'description',
        'target_system',
        'action_required',
        'resolved_by_id',
        'resolved_at',
        'resolution_notes',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
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
        return $this->belongsTo(Submission::class, 'proposal_id');
    }

    public function issue(): BelongsTo
    {
        return $this->belongsTo(DecisionIssue::class, 'issue_id');
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'resolved_by_id');
    }

    public function isCritical(): bool
    {
        return $this->severity === 'critical';
    }

    public function isWarning(): bool
    {
        return $this->severity === 'warning';
    }

    public function isResolved(): bool
    {
        return $this->resolved_at !== null;
    }
}