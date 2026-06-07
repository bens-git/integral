<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EmbeddingService
{
    /**
     * Get embedding for given text using configured provider.
     * Supported providers: 'openai' (default) and 'google' (Generative Language / AI Studio).
     *
     * Configuration (in .env):
     * - EMBEDDING_PROVIDER=google|openai
     * - OPENAI_API_KEY
     * - OPENAI_EMBEDDING_MODEL (optional)
     * - GOOGLE_API_KEY
     * - GOOGLE_EMBEDDING_MODEL (optional)
     * - GOOGLE_EMBEDDING_URL (optional, defaults to Generative Language embed endpoint pattern)
     */
    public static function getEmbedding(string $text): ?array
    {
        $provider = env('EMBEDDING_PROVIDER');

        // auto-detect if not explicitly set
        if (empty($provider)) {
            if (!empty(env('GOOGLE_API_KEY'))) {
                $provider = 'google';
            } else {
                $provider = 'openai';
            }
        }

        if ($provider === 'google') {
            $apiKey = env('GOOGLE_API_KEY');
            if (empty($apiKey)) {
                throw new \Exception('GOOGLE_API_KEY not configured');
            }

            $model = env('GOOGLE_EMBEDDING_MODEL', 'textembedding-gecko-001');
            $defaultEndpoint = "https://generativelanguage.googleapis.com/v1beta2/models/{$model}:embedText";
            $endpoint = env('GOOGLE_EMBEDDING_URL', $defaultEndpoint);

            // Google accepts API key as query param for simple API key use-cases.
            $url = $endpoint . (strpos($endpoint, '?') === false ? "?key={$apiKey}" : "&key={$apiKey}");

            $resp = Http::post($url, [
                'text' => $text,
            ]);

            if (!$resp->successful()) {
                throw new \Exception('Google embedding API error: ' . $resp->body());
            }

            $data = $resp->json();

            // Try a few common response shapes
            if (!empty($data['embeddings'][0]['embedding'])) {
                return $data['embeddings'][0]['embedding'];
            }
            if (!empty($data['data'][0]['embedding'])) {
                return $data['data'][0]['embedding'];
            }
            if (!empty($data['embedding'])) {
                return $data['embedding'];
            }

            // If unknown shape, log and return null
            Log::warning('Unknown Google embedding response shape', ['response' => $data]);
            return null;
        }

        // Default: OpenAI-compatible embeddings API
        $apiKey = env('OPENAI_API_KEY');
        if (empty($apiKey)) {
            throw new \Exception('OPENAI_API_KEY not configured');
        }

        $model = env('OPENAI_EMBEDDING_MODEL', 'text-embedding-3-small');

        $resp = Http::withToken($apiKey)
            ->post('https://api.openai.com/v1/embeddings', [
                'model' => $model,
                'input' => $text,
            ]);

        if (!$resp->successful()) {
            throw new \Exception('OpenAI embedding API error: ' . $resp->body());
        }

        $data = $resp->json();
        if (!empty($data['data'][0]['embedding'])) {
            return $data['data'][0]['embedding'];
        }

        return null;
    }

    public static function cosineSimilarity(array $a, array $b): float
    {
        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;
        $len = min(count($a), count($b));
        for ($i = 0; $i < $len; $i++) {
            $dot += $a[$i] * $b[$i];
            $normA += $a[$i] * $a[$i];
            $normB += $b[$i] * $b[$i];
        }
        if ($normA <= 0 || $normB <= 0) return 0.0;
        return $dot / (sqrt($normA) * sqrt($normB));
    }
}
