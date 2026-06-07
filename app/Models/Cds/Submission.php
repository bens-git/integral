<?php

namespace App\Models\Cds;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Jobs\ClusterSubmission;
use Illuminate\Support\Facades\Log;

class Submission extends Model
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
        'submission_type',
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

        // After a submission is created, dispatch clustering job to group similar submissions.
        static::created(function ($model) {
            try {
                ClusterSubmission::dispatch($model->id);
            } catch (\Throwable $e) {
                Log::error('Cluster dispatch failed: ' . $e->getMessage(), ['submission_id' => $model->id]);
            }
        });
    }

    /**
     * Submissions can belong to many clusters.
     */
    public function clusters(): BelongsToMany
    {
        return $this->belongsToMany(SubmissionCluster::class, 'submission_cluster_members', 'submission_id', 'cluster_id')
            ->withPivot('similarity')
            ->withTimestamps();
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'submitter_id');
    }

    public function validationEvents(): HasMany
    {
        return $this->hasMany(ValidationEvent::class, 'submission_id');
    }

    public function decisionIssue(): BelongsTo
    {
        return $this->belongsTo(DecisionIssue::class, 'submission_id', 'submission_id');
    }

    public function systemSignals(): HasMany
    {
        return $this->hasMany(SystemSignal::class, 'submission_id');
    }

    public function supersedes(): BelongsTo
    {
        return $this->belongsTo(Submission::class, 'supersedes_id');
    }

    public function amends(): BelongsTo
    {
        return $this->belongsTo(Submission::class, 'amends_id');
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
