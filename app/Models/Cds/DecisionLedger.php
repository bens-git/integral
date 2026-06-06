<?php

namespace App\Models\Cds;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DecisionLedger extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'issue_id',
        'consensus_id',
        'node_id',
        'participant_id',
        'event_type',
        'title',
        'description',
        'hash',
        'previous_hash_id',
        'signature',
        'is_verified',
        'verified_at',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
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

    public function consensus(): BelongsTo
    {
        return $this->belongsTo(ConsensusModel::class, 'consensus_id');
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'participant_id');
    }

    public function revisionLinksAsPrior(): HasMany
    {
        return $this->hasMany(DecisionRevisionLink::class, 'prior_decision_id');
    }

    public function revisionLinksAsRevised(): HasMany
    {
        return $this->hasMany(DecisionRevisionLink::class, 'revised_decision_id');
    }

    public function isVerified(): bool
    {
        return $this->is_verified;
    }

    public function generateHash(): string
    {
        $data = json_encode([
            'issue_id' => $this->issue_id,
            'event_type' => $this->event_type,
            'title' => $this->title,
            'description' => $this->description,
            'created_at' => $this->created_at?->toIso8601String(),
        ]);

        return hash('sha256', $data);
    }
}