<?php

namespace App\Models\Cds;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KnowledgeNode extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'node_id',
        'parent_id',
        'created_by_id',
        'type',
        'title',
        'content',
        'summary',
        'source',
        'source_url',
        'confidence_level',
        'domain',
        'is_verified',
        'verified_by_id',
        'verified_at',
        'license',
        'version',
        'supersedes_id',
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

    public function parent(): BelongsTo
    {
        return $this->belongsTo(KnowledgeNode::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(KnowledgeNode::class, 'parent_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'created_by_id');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'verified_by_id');
    }

    public function issueMappings(): HasMany
    {
        return $this->hasMany(IssueKnowledgeMap::class, 'knowledge_id');
    }

    public function issues(): BelongsToMany
    {
        return $this->belongsToMany(DecisionIssue::class, 'issue_knowledge_map', 'knowledge_id', 'issue_id');
    }

    public function supersedes(): BelongsTo
    {
        return $this->belongsTo(KnowledgeNode::class, 'supersedes_id');
    }

    public function isVerified(): bool
    {
        return $this->is_verified;
    }

    public function isHighConfidence(): bool
    {
        return $this->confidence_level === 'high' || $this->confidence_level === 'verified';
    }
}