<?php

namespace App\Jobs;

use App\Models\Cds\Submission;
use App\Models\Cds\SubmissionCluster;
use App\Services\EmbeddingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClusterSubmission implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $submissionId;

    public function __construct(string $submissionId)
    {
        $this->submissionId = $submissionId;
    }

    public function handle(): void
    {
        $submission = Submission::find($this->submissionId);
        if (!$submission) {
            Log::warning('ClusterSubmission: submission not found', ['id' => $this->submissionId]);
            return;
        }

        // Ensure we have an embedding
        try {
            $embedding = $submission->embedding;
            if (empty($embedding) || !is_array($embedding)) {
                $text = trim(($submission->title ?? '') . "\n\n" . ($submission->description ?? ''));
                $embedding = EmbeddingService::getEmbedding($text);
                if (empty($embedding) || !is_array($embedding)) {
                    Log::error('ClusterSubmission: could not compute embedding', ['id' => $submission->id]);
                    return;
                }

                // persist embedding on submission
                $submission->embedding = $embedding;
                $submission->save();
            }
        } catch (\Throwable $e) {
            Log::error('ClusterSubmission embedding error: ' . $e->getMessage(), ['id' => $submission->id]);
            return;
        }

        $threshold = (float) env('SUBMISSION_CLUSTER_SIMILARITY', 0.85);

        // Find candidate clusters with centroids
        $clusters = SubmissionCluster::whereNotNull('centroid')->get();
        $best = null;
        $bestSim = 0.0;

        foreach ($clusters as $cluster) {
            $centroid = $cluster->centroid;
            if (!is_array($centroid) || count($centroid) !== count($embedding)) continue;
            $sim = EmbeddingService::cosineSimilarity($embedding, $centroid);
            if ($sim > $bestSim) {
                $bestSim = $sim;
                $best = $cluster;
            }
        }

        DB::beginTransaction();
        try {
            if ($best && $bestSim >= $threshold) {
                // attach to best cluster
                $best->submissions()->attach($submission->id, ['similarity' => $bestSim]);

                // recompute centroid as simple average
                $count = max(1, $best->submissions_count);
                $oldCentroid = $best->centroid ?? [];
                $newCentroid = [];
                $len = min(count($oldCentroid), count($embedding));
                if ($len === 0) {
                    $newCentroid = $embedding;
                } else {
                    for ($i = 0; $i < $len; $i++) {
                        $a = $oldCentroid[$i] ?? 0.0;
                        $b = $embedding[$i];
                        $newCentroid[$i] = ($a * $count + $b) / ($count + 1);
                    }
                }

                $best->centroid = $newCentroid ?: $embedding;
                $best->submissions_count = $best->submissions_count + 1;
                $best->save();
            } else {
                // create new cluster
                $cluster = SubmissionCluster::create([
                    'id' => (string) \Illuminate\Support\Str::uuid(),
                    'title' => mb_strimwidth($submission->title ?? '', 0, 140, '...'),
                    'summary' => mb_strimwidth($submission->description ?? '', 0, 500, '...'),
                    'centroid' => $embedding,
                    'submissions_count' => 1,
                ]);

                $cluster->submissions()->attach($submission->id, ['similarity' => null]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('ClusterSubmission handle error: ' . $e->getMessage(), ['id' => $submission->id]);
        }
    }
}
