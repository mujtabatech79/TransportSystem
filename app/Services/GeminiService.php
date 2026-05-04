<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;

class GeminiService
{
    protected $maxRequestsPerMinute = 5;

    protected $apiKeys   = [];
    protected $groqKey   = null;

    protected $geminiModels = [
        'gemini-2.0-flash-lite',
        'gemini-2.0-flash',
        'gemini-1.5-flash',
        'gemini-1.5-flash-8b',
    ];

    protected $groqModels = [
        'llama-3.3-70b-versatile',
        'llama-3.1-8b-instant',
        'gemma2-9b-it',
    ];

    public function __construct()
    {
        foreach (['GEMINI_API_KEY_1','GEMINI_API_KEY_2','GEMINI_API_KEY_3','GEMINI_API_KEY_4'] as $envKey) {
            $val = env($envKey);
            if ($val && strlen(trim($val)) > 10) {
                $this->apiKeys[] = trim($val);
            }
        }

        $legacy = env('GEMINI_API_KEY');
        if ($legacy && !in_array(trim($legacy), $this->apiKeys)) {
            array_unshift($this->apiKeys, trim($legacy));
        }

        $this->apiKeys = array_unique($this->apiKeys);
        $this->groqKey = env('GROQ_API_KEY') ?: env('groq_ApI_key');

        if (empty($this->apiKeys) && !$this->groqKey) {
            Log::error('GeminiService: No API keys configured in .env');
        }

        Log::info('GeminiService: Loaded ' . count($this->apiKeys) . ' Gemini key(s)' . ($this->groqKey ? ' + Groq' : ''));
    }

    // ══════════════════════════════════════════════════════════
    // MAIN GENERATE
    // ══════════════════════════════════════════════════════════

    public function generate(string $prompt, string $systemPrompt = null): ?string
    {
        $cacheKey = 'ai_resp_' . md5(trim($prompt));
        if (Cache::has($cacheKey)) {
            Log::info('AI: cache hit');
            return Cache::get($cacheKey);
        }

        $body = $this->buildGeminiBody($prompt, $systemPrompt);

        foreach ($this->apiKeys as $keyIndex => $apiKey) {
            foreach ($this->geminiModels as $model) {

                $slotKey = 'gemini_slot_' . md5($apiKey . $model);

                if (Cache::has('quota_out_' . $slotKey)) {
                    continue;
                }

                $rpmKey = 'gemini_rpm_' . $slotKey;
                if (RateLimiter::tooManyAttempts($rpmKey, $this->maxRequestsPerMinute)) {
                    continue;
                }

                $result = $this->callGemini($apiKey, $model, $body);

                if ($result['success']) {
                    RateLimiter::hit($rpmKey, 60);
                    Cache::put($cacheKey, $result['text'], now()->addMinutes(30));
                    Log::info("Gemini: success key#{$keyIndex} model:{$model}");
                    return $result['text'];
                }

                if ($result['status'] === 429 || $result['status'] === 403) {
                    Log::warning("Gemini: key#{$keyIndex} model:{$model} → {$result['status']}");
                    $this->markExhausted($slotKey, $result['error']);
                    for ($i = 0; $i <= $this->maxRequestsPerMinute; $i++) {
                        RateLimiter::hit($rpmKey, 60);
                    }
                    continue;
                }

                if ($result['status'] === 404) {
                    continue;
                }

                Log::warning("Gemini: key#{$keyIndex} model:{$model} error: " . $result['error']);
            }
        }

        if ($this->groqKey) {
            Log::info('AI: All Gemini exhausted — trying Groq fallback');
            $groqResult = $this->callGroq($prompt, $systemPrompt);
            if ($groqResult) {
                Cache::put($cacheKey, $groqResult, now()->addMinutes(30));
                return $groqResult;
            }
        }

        Log::error('AI: All providers exhausted — using smart fallback');
        return null;
    }

    // ══════════════════════════════════════════════════════════
    // GEMINI API CALL
    // ══════════════════════════════════════════════════════════

    protected function callGemini(string $apiKey, string $model, array $body): array
    {
        try {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

            $response = Http::timeout(20)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, $body);

            $status = $response->status();

            if ($response->successful()) {
                $data = $response->json();
                $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                if ($text && strlen(trim($text)) > 2) {
                    return ['success' => true, 'text' => trim($text), 'status' => 200];
                }
                return ['success' => false, 'error' => 'Empty response', 'status' => 200];
            }

            $errorData = $response->json();
            $errorMsg  = $errorData['error']['message'] ?? $response->body();
            return ['success' => false, 'error' => $errorMsg, 'status' => $status];

        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage(), 'status' => 0];
        }
    }

    // ══════════════════════════════════════════════════════════
    // GROQ API CALL
    // ══════════════════════════════════════════════════════════

    protected function callGroq(string $prompt, ?string $systemPrompt): ?string
    {
        if (!$this->groqKey) return null;

        foreach ($this->groqModels as $model) {
            $slotKey = 'groq_slot_' . md5($this->groqKey . $model);
            if (Cache::has('quota_out_' . $slotKey)) continue;

            try {
                $messages = [];
                if ($systemPrompt) {
                    $messages[] = ['role' => 'system', 'content' => $systemPrompt];
                }
                $messages[] = ['role' => 'user', 'content' => $prompt];

                $response = Http::timeout(20)
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . $this->groqKey,
                        'Content-Type'  => 'application/json',
                    ])
                    ->post('https://api.groq.com/openai/v1/chat/completions', [
                        'model'       => $model,
                        'messages'    => $messages,
                        'max_tokens'  => 350,
                        'temperature' => 0.75,
                    ]);

                if ($response->successful()) {
                    $text = $response->json()['choices'][0]['message']['content'] ?? null;
                    if ($text && strlen(trim($text)) > 2) {
                        Log::info("Groq: success with {$model}");
                        return trim($text);
                    }
                }

                if ($response->status() === 429) {
                    $this->markExhausted($slotKey, 'rate_limited');
                    continue;
                }

                Log::warning("Groq: {$model} failed: " . $response->status());

            } catch (\Exception $e) {
                Log::warning("Groq: exception on {$model}: " . $e->getMessage());
            }
        }

        return null;
    }

    // ══════════════════════════════════════════════════════════
    // HELPERS
    // ══════════════════════════════════════════════════════════

    protected function markExhausted(string $slotKey, string $errorMsg): void
    {
        $isDailyQuota = str_contains($errorMsg, 'quota') || str_contains($errorMsg, 'exceeded');

        if ($isDailyQuota) {
            $nowPKT   = now()->timezone('Asia/Karachi');
            $midnight = $nowPKT->copy()->addDay()->startOfDay();
            $seconds  = $nowPKT->diffInSeconds($midnight);
            Cache::put('quota_out_' . $slotKey, true, $seconds);
            Log::warning("AI: slot exhausted until midnight PKT ({$seconds}s)");
        } else {
            $retryIn = $this->parseRetryAfter($errorMsg);
            Cache::put('quota_out_' . $slotKey, true, max($retryIn, 30));
        }
    }

    protected function buildGeminiBody(string $prompt, ?string $systemPrompt): array
    {
        $fullPrompt = $systemPrompt
            ? "SYSTEM INSTRUCTIONS:\n{$systemPrompt}\n\n---\nUSER:\n{$prompt}"
            : $prompt;

        return [
            'contents' => [
                ['role' => 'user', 'parts' => [['text' => $fullPrompt]]]
            ],
            'generationConfig' => [
                'temperature'     => 0.75,
                'maxOutputTokens' => 350,
                'topP'            => 0.92,
            ],
            'safetySettings' => [
                ['category' => 'HARM_CATEGORY_HARASSMENT',        'threshold' => 'BLOCK_NONE'],
                ['category' => 'HARM_CATEGORY_HATE_SPEECH',       'threshold' => 'BLOCK_NONE'],
                ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_NONE'],
                ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_NONE'],
            ],
        ];
    }

    protected function parseRetryAfter(string $errorMsg): int
    {
        if (preg_match('/retry in (\d+(?:\.\d+)?)s/i', $errorMsg, $m)) {
            return (int) ceil((float) $m[1]);
        }
        return 60;
    }

    // ══════════════════════════════════════════════════════════
    // STATUS & TEST
    // ══════════════════════════════════════════════════════════

    public function getQuotaStatus(): array
    {
        $status = [];
        foreach ($this->apiKeys as $i => $apiKey) {
            foreach ($this->geminiModels as $model) {
                $slotKey = 'gemini_slot_' . md5($apiKey . $model);
                $rpmKey  = 'gemini_rpm_' . $slotKey;
                $exhausted = Cache::has('quota_out_' . $slotKey);
                $rpmLimited = RateLimiter::tooManyAttempts($rpmKey, $this->maxRequestsPerMinute);
                $status["key{$i}_{$model}"] = [
                    'locally_limited' => $exhausted || $rpmLimited,
                    'quota_exhausted' => $exhausted,
                    'rpm_limited'     => $rpmLimited,
                ];
            }
        }
        if ($this->groqKey) {
            $anyGroqAvail = false;
            foreach ($this->groqModels as $model) {
                if (!Cache::has('quota_out_groq_slot_' . md5($this->groqKey . $model))) {
                    $anyGroqAvail = true;
                }
            }
            $status['groq_available'] = ['locally_limited' => !$anyGroqAvail];
        }
        return $status;
    }

    public function testConnection(): array
    {
        $results      = [];
        $workingModel = null;

        foreach ($this->apiKeys as $i => $apiKey) {
            foreach ($this->geminiModels as $model) {
                if ($this->isSlotExhausted($apiKey, $model)) {
                    $results["key{$i}_{$model}"] = ['success' => false, 'message' => 'Quota exhausted (cached)'];
                    continue;
                }

                $body   = ['contents' => [['role' => 'user', 'parts' => [['text' => 'Say OK']]]], 'generationConfig' => ['maxOutputTokens' => 5]];
                $result = $this->callGemini($apiKey, $model, $body);

                $results["key{$i}_{$model}"] = [
                    'success' => $result['success'],
                    'status'  => $result['status'],
                    'message' => $result['success'] ? '✅ Working' : $result['error'],
                ];

                if ($result['success'] && !$workingModel) {
                    $workingModel = "key{$i}_{$model}";
                }

                if ($result['status'] === 429 || $result['status'] === 403) {
                    $slotKey = 'gemini_slot_' . md5($apiKey . $model);
                    $this->markExhausted($slotKey, $result['error']);
                }
            }
        }

        if ($this->groqKey) {
            $groqResult = $this->callGroq('Say OK in one word', null);
            $results['groq'] = $groqResult
                ? ['success' => true,  'message' => '✅ Groq working']
                : ['success' => false, 'message' => 'Groq failed'];
            if ($groqResult && !$workingModel) $workingModel = 'groq';
        }

        return $workingModel
            ? ['success' => true,  'working_model' => $workingModel, 'details' => $results,
               'summary' => count($this->apiKeys) . ' Gemini key(s) + ' . ($this->groqKey ? 'Groq' : 'no Groq')]
            : ['success' => false, 'error' => 'All providers exhausted. Smart fallback is ACTIVE.',
               'details' => $results, 'note' => 'Chatbot still works via NLP fallback!'];
    }

    private function isSlotExhausted(string $apiKey, string $model): bool
    {
        return Cache::has('quota_out_gemini_slot_' . md5($apiKey . $model));
    }



    public function getApiKeys(): array
{
    return $this->apiKeys;
}
}