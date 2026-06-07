<?php

namespace App\Models\Cds;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Participant extends Authenticatable
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'node_id',
        'name',
        'email',
        'role',
        'status',
        'email_verified_at',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function proposals(): HasMany
    {
        return $this->hasMany(Submission::class, 'submitter_id');
    }

    public function validationEvents(): HasMany
    {
        return $this->hasMany(ValidationEvent::class, 'validator_id');
    }

    public function deliberationMessages(): HasMany
    {
        return $this->hasMany(DeliberationMessage::class, 'participant_id');
    }

    public function objections(): HasMany
    {
        return $this->hasMany(Objection::class, 'participant_id');
    }

    public function feedbackEvents(): HasMany
    {
        return $this->hasMany(FeedbackEvent::class, 'participant_id');
    }

    public function decisionRevisionLinks(): HasMany
    {
        return $this->hasMany(DecisionRevisionLink::class, 'participant_id');
    }

    public function createdKnowledgeNodes(): HasMany
    {
        return $this->hasMany(KnowledgeNode::class, 'created_by_id');
    }

    public function verifiedKnowledgeNodes(): HasMany
    {
        return $this->hasMany(KnowledgeNode::class, 'verified_by_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isFacilitator(): bool
    {
        return $this->role === 'facilitator' || $this->role === 'admin';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}