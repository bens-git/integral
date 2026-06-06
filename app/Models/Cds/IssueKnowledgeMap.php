<?php

namespace App\Models\Cds;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IssueKnowledgeMap extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'issue_id',
        'knowledge_id',
        'added_by_id',
        'relevance',
        'weight',
        'context_notes',
    ];

    protected $casts = [
        'weight' => 'integer',
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

    public function knowledgeNode(): BelongsTo
    {
        return $this->belongsTo(KnowledgeNode::class, 'knowledge_id');
    }

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'added_by_id');
    }

    public function isSupporting(): bool
    {
        return $this->relevance === 'supporting';
    }

    public function isContradicting(): bool
    {
        return $this->relevance === 'contradicting';
    }

    public function isContextual(): bool
    {
        return $this->relevance === 'contextual';
    }

    public function isFoundational(): bool
    {
        return $this->relevance === 'foundational';
    }
}