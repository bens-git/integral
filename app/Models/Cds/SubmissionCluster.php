<?php

namespace App\Models\Cds;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SubmissionCluster extends Model
{
    use HasFactory;

    protected $table = 'submission_clusters';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'decision_issue_id',
        'title',
        'summary',
        'centroid',
        'submissions_count',
    ];

    protected $casts = [
        'centroid' => 'array',
    ];

    public function submissions(): BelongsToMany
    {
        return $this->belongsToMany(Submission::class, 'submission_cluster_members', 'cluster_id', 'submission_id')
            ->withPivot('similarity')
            ->withTimestamps();
    }
}
