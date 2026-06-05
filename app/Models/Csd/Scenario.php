<?php

namespace App\Models\Csd;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Scenario extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'issue_id',
        'created_by_id',
        'title',
        'description',
        'assumptions',
        'methodology',
        'viability_score',
        'risk_score',
        'impact_score',
        'status',
        'based_on_id',
    ];

    protected $casts = [
        'viability_score' => 'decimal:2',
        'risk_score' => 'decimal:2',
        'impact_score' => 'decimal:2',
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

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'created_by_id');
    }

    public function basedOn(): BelongsTo
    {
        return $this->belongsTo(Scenario::class, 'based_on_id');
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isModeling(): bool
    {
        return $this->status === 'modeling';
    }

    public function isComplete(): bool
    {
        return $this->status === 'complete';
    }
}