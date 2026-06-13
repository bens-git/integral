<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClusterAIService
{
    private static function provider(): string
    {
        $provider = env('CLUSTER_AI_PROVIDER');

        if (empty($provider)) {
            if (!empty(env('GOOGLE_API_KEY'))) {
                $provider = 'google';
            } else {
                $provider = 'openai';
            }
        }

        return $provider;
    }

    private static function buildPrompt(string $text, bool $isSummarize): string
    {
        $action = $isSummarize
            ? 'Update the cluster name and description so it reflects ALL the included submissions. Keep it concise.'
            : 'Create a concise cluster name and description for the following submission text.';

        return <<<PROMPT
You are a strict JSON API. {$action}

Return ONLY valid JSON in this shape:
{
  "title": "Short cluster title",
  "summary": "1-2 sentence description of the cluster themes",
  "keywords": ["keyword1", "keyword2", "keyword3"]
}

Submission text:
---
{$text}
---

JSON:
PROMPT;
    }

    private static function callOpenAI(string $prompt): ?array
    {
        $apiKey = env('OPENAI_API_KEY');
        if (empty($apiKey)) {
            Log::warning('OPENAI_API_KEY not configured for ClusterAIService');
            return null;
        }

        $model = env('OPENAI_CLUSTER_AI_MODEL', 'gpt-4o-mini');

        $resp = Http::withToken($apiKey)
            ->timeout(60)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => $model,
                'temperature' => 0.2,
                'response_format' => ['type' => 'json_object'],
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful assistant that always responds with valid JSON.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

        if (!$resp->successful()) {
            Log::warning('OpenAI cluster AI error', ['status' => $resp->status(), 'body' => $resp->body()]);
            return null;
        }

        $data = $resp->json();
        $content = $data['choices'][0]['message']['content'] ?? null;

        if (!$content) {
            return null;
        }

        $decoded = json_decode($content, true);

        if (!is_array($decoded)) {
            return null;
        }

        return [
            'title' => (string) ($decoded['title'] ?? 'Cluster'),
            'summary' => (string) ($decoded['summary'] ?? ''),
            'keywords' => is_array($decoded['keywords'] ?? null) ? $decoded['keywords'] : [],
        ];
    }

    private static function callGoogle(string $prompt): ?array
    {
        $apiKey = env('GOOGLE_API_KEY');
        if (empty($apiKey)) {
            Log::warning('GOOGLE_API_KEY not configured for ClusterAIService');
            return null;
        }

        $model = env('GOOGLE_CLUSTER_AI_MODEL', 'gemini-2.0-flash');
        $model = ltrim(trim($model, "\"'"), '/');
        $url = "https://generativelanguage.googleapis.com/v1beta/{$model}:generateContent?key={$apiKey}";

        $payload = json_encode([
            'contents' => [
                ['parts' => [['text' => $prompt]]],
            ],
            'generationConfig' => [
                'temperature' => 0.2,
            ],
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
            Log::warning('Google cluster AI error', ['status' => $httpCode, 'body' => $response]);
            return null;
        }

        $data = json_decode($response, true);
        $candidate = $data['candidates'][0] ?? null;
        $text = $candidate['content']['parts'][0]['text'] ?? null;

        if (!$text) {
            return null;
        }

        $clean = trim($text);
        if (str_starts_with($clean, '```json')) {
            $clean = substr($clean, 7);
        } elseif (str_starts_with($clean, '```')) {
            $clean = substr($clean, 3);
        }
        if (str_ends_with($clean, '```')) {
            $clean = substr($clean, 0, -3);
        }
        $clean = trim($clean);

        $decoded = json_decode($clean, true);

        if (!is_array($decoded)) {
            return null;
        }

        return [
            'title' => (string) ($decoded['title'] ?? 'Cluster'),
            'summary' => (string) ($decoded['summary'] ?? ''),
            'keywords' => is_array($decoded['keywords'] ?? null) ? $decoded['keywords'] : [],
        ];
    }

    private static function generate(string $prompt): ?array
    {
        $provider = self::provider();

        if ($provider === 'google') {
            return self::callGoogle($prompt);
        }

        return self::callOpenAI($prompt);
    }

    public static function createCluster(string $text): array
    {
        $result = self::generate(self::buildPrompt($text, false));

        if ($result) {
            return $result;
        }

        return [
            'title' => mb_strimwidth($text, 0, 80, '...'),
            'summary' => mb_strimwidth($text, 0, 220, '...'),
            'keywords' => [],
        ];
    }

    public static function summarize(\App\Models\Cds\SubmissionCluster $cluster): array
    {
        $text = trim(
            ($cluster->title ?? '') .
            "\n\n" .
            ($cluster->summary ?? '') .
            "\n\n" .
            implode(', ', (array) ($cluster->keywords ?? []))
        );

        $result = self::generate(self::buildPrompt($text, true));

        if ($result) {
            return $result;
        }

        return [
            'title' => (string) $cluster->title,
            'summary' => (string) $cluster->summary,
            'keywords' => (array) ($cluster->keywords ?? []),
        ];
    }
}
