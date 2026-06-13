<?php

namespace App\Jobs;

use App\Models\Cds\Submission;
use App\Models\Cds\SubmissionCluster;
use App\Services\EmbeddingService;
use App\Services\ClusterAIService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ClusterSubmission implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $submissionId
    ) {}

    public function handle(): void
    {
        $submission = Submission::find($this->submissionId);

        if (!$submission) {
            return;
        }

        $text = trim(
            ($submission->title ?? '') .
            "\n\n" .
            ($submission->description ?? '')
        );

        $embedding = $submission->embedding;

        if (!$embedding) {
            $embedding = EmbeddingService::getEmbedding($text);

            if (!$embedding) {
                Log::error('ClusterSubmission: could not compute embedding', ['id' => $submission->id]);
                return;
            }

            $submission->update([
                'embedding' => $embedding,
                'embedding_model' => 'text-embedding-model',
            ]);
        }

        $clusters = SubmissionCluster::nearest(
            $embedding,
            limit: 10
        );

        $best = null;
        $score = 0;

        foreach ($clusters as $cluster) {
            $centroid = $cluster->centroid;

            if (!is_array($centroid) || count($centroid) !== count($embedding)) {
                continue;
            }

            $similarity = EmbeddingService::cosineSimilarity(
                $embedding,
                $centroid
            );

            if ($similarity > $score) {
                $score = $similarity;
                $best = $cluster;
            }
        }

        DB::transaction(function () use ($submission, $embedding, $best, $score, $text) {
            if ($best && $score >= config('cds.cluster_threshold', 0.85)) {
                $best = SubmissionCluster::lockForUpdate()->find($best->id);

                $best->submissions()
                    ->syncWithoutDetaching([
                        $submission->id => ['similarity' => $score]
                    ]);

                $best->centroid = $this->updateCentroid(
                    $best->centroid,
                    $embedding,
                    $best->submissions_count
                );

                $best->submissions_count++;

                if ($best->submissions_count % 10 === 0) {
                    $ai = ClusterAIService::summarize($best);

                    $best->update([
                        'title' => $ai['title'],
                        'summary' => $ai['summary'],
                        'keywords' => $ai['keywords'],
                    ]);
                }

                $best->save();

                return;
            }

            $ai = ClusterAIService::createCluster($text);

            $cluster = SubmissionCluster::create([
                'id' => (string) Str::uuid(),
                'title' => $ai['title'],
                'summary' => $ai['summary'],
                'keywords' => $ai['keywords'],
                'centroid' => $embedding,
                'submissions_count' => 1,
                'confidence' => 1.0,
            ]);

            $cluster->submissions()->attach(
                $submission->id
            );
        });
    }

    private function updateCentroid(
        array $old,
        array $new,
        int $count
    ): array {
        $result = [];
        $len = min(count($old), count($new));

        for ($i = 0; $i < $len; $i++) {
            $result[$i] = (($old[$i] * $count) + $new[$i]) / ($count + 1);
        }

        return $result;
    }
}
