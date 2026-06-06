<?php

namespace App\Models\Cds;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ValidationEvent extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'proposal_id',
        'validator_id',
        'node_id',
        'result',
        'notes',
        'validation_type',
        'is_blocking',
        'supersedes_id',
    ];

    protected $casts = [
        'is_blocking' => 'boolean',
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
        return $this->belongsTo(Proposal::class, 'proposal_id');
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'validator_id');
    }

    public function supersedes(): BelongsTo
    {
        return $this->belongsTo(ValidationEvent::class, 'supersedes_id');
    }

    public function isValid(): bool
    {
        return $this->result === 'valid';
    }

    public function isInvalid(): bool
    {
        return $this->result === 'invalid';
    }

    public function needsRevision(): bool
    {
        return $this->result === 'needs_revision';
    }

    public function isPending(): bool
    {
        return $this->result === 'pending';
    }
}