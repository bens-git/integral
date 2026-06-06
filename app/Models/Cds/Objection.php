<?php

namespace App\Models\Cds;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Objection extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'consensus_id',
        'participant_id',
        'node_id',
        'objection_strength',
        'objection_type',
        'reason',
        'proposed_resolution',
        'status',
        'is_blocking',
        'addressed_by_id',
        'resolution_notes',
        'addressed_at',
        'resolved_at',
    ];

    protected $casts = [
        'objection_strength' => 'decimal:2',
        'is_blocking' => 'boolean',
        'addressed_at' => 'datetime',
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

    public function consensus(): BelongsTo
    {
        return $this->belongsTo(ConsensusModel::class, 'consensus_id');
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'participant_id');
    }

    public function addressedBy(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'addressed_by_id');
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isAddressed(): bool
    {
        return $this->status === 'addressed';
    }

    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }

    public function isWithdrawn(): bool
    {
        return $this->status === 'withdrawn';
    }

    public function isUpheld(): bool
    {
        return $this->status === 'upheld';
    }
}