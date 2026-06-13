<?php

namespace App\Models\Cds;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

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
        'keywords',
        'centroid',
        'submissions_count',
        'confidence',
    ];

    protected $casts = [
        'centroid' => 'array',
        'keywords' => 'array',
        'confidence' => 'float',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function submissions(): BelongsToMany
    {
        return $this->belongsToMany(Submission::class, 'submission_cluster_members', 'cluster_id', 'submission_id')
            ->withPivot('similarity')
            ->withTimestamps();
    }

    public function scopeNearest($query, array $embedding, int $limit = 10)
    {
        return $query->whereNotNull('centroid')->limit($limit);
    }
}
