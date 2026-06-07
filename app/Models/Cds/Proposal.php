<?php

namespace App\Models\Cds;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proposal extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'submitter_id',
        'node_id',
        'title',
        'description',
        'summary',
        'content',
        'status',
        'category',
        'priority',
        'scope',
        'version',
        'supersedes_id',
        'is_amendment',
        'amends_id',
        'embedding',
    ];

    protected $casts = [
        'embedding' => 'array',
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

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'submitter_id');
    }

    public function validationEvents(): HasMany
    {
        return $this->hasMany(ValidationEvent::class, 'proposal_id');
    }

    public function decisionIssue(): BelongsTo
    {
        return $this->belongsTo(DecisionIssue::class, 'proposal_id', 'proposal_id');
    }

    public function systemSignals(): HasMany
    {
        return $this->hasMany(SystemSignal::class, 'proposal_id');
    }

    public function supersedes(): BelongsTo
    {
        return $this->belongsTo(Proposal::class, 'supersedes_id');
    }

    public function amends(): BelongsTo
    {
        return $this->belongsTo(Proposal::class, 'amends_id');
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isImplemented(): bool
    {
        return $this->status === 'implemented';
    }
}