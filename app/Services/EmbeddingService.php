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
            // Read the key and strip spaces, single quotes, and double quotes
            $apiKey = env('GOOGLE_API_KEY');
            $apiKey = trim($apiKey);
            $apiKey = trim($apiKey, '"\'');

            if (empty($apiKey)) {
                Log::warning('GOOGLE_API_KEY not configured, returning null embedding');
                return null;
            }

            $model = env('GOOGLE_EMBEDDING_MODEL', 'models/gemini-embedding-2');
            $model = trim($model);
            $model = trim($model, '"\'');
            $model = ltrim($model, '/');
            
            // Explicit, raw endpoint structure
            $url = "https://generativelanguage.googleapis.com/v1beta/{$model}:embedContent?key={$apiKey}";

            $payload = json_encode([
                'content' => [
                    'parts' => [
                        ['text' => $text]
                    ]
                ]
            ]);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200) {
                throw new \Exception('Google embedding API error (Status ' . $httpCode . '): ' . $response);
            }

            $data = json_decode($response, true);

            if (!empty($data['embedding']['values'])) {
                return $data['embedding']['values'];
            }

            Log::warning('Unknown Google embedding response shape', ['response' => $data]);
            return null;
        }



        // Default: OpenAI-compatible embeddings API
        $apiKey = env('OPENAI_API_KEY');
        if (empty($apiKey)) {
            Log::warning('OPENAI_API_KEY not configured, returning null embedding');
            return null;
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
