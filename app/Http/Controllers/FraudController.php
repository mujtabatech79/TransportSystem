<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\Userr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class FraudController extends Controller
{
    // ══════════════════════════════════════════════════════════
    // FRAUD SCORING WEIGHTS
    // ══════════════════════════════════════════════════════════
    const WEIGHT_CHASSIS       = 50;   // Must match — highest weight
    const WEIGHT_VEHICLE_NO    = 30;   // Regno match
    const WEIGHT_IMAGE_QUALITY = 10;   // Image size / blur check
    const WEIGHT_OWNER_NAME    = 5;    // Owner name similarity
    const WEIGHT_VEHICLE_TYPE  = 5;    // Vehicle type match
    const FRAUD_THRESHOLD      = 60;   // >= 60 → Not Fraud, < 60 → Fraud

    // ══════════════════════════════════════════════════════════
    // SHOW PENDING VEHICLES (2 Categories)
    // ══════════════════════════════════════════════════════════
    public function fraudPendingVehicles()
    {
        $pendingVehicles = Vehicle::where('status', 'pending')
            ->with('user')
            ->paginate(12);

        // Vehicles already analyzed
        $fraudVehicles    = Vehicle::where('fraud_status', 'fraud')
            ->with('user')
            ->latest()
            ->take(20)
            ->get();

        $notFraudVehicles = Vehicle::where('fraud_status', 'not_fraud')
            ->with('user')
            ->latest()
            ->take(20)
            ->get();

        $approvedCount = Vehicle::where('status', 'approved')->count();
        $rejectedCount = Vehicle::where('status', 'rejected')->count();
        $totalCount    = Vehicle::count();
        $fraudCount    = Vehicle::where('fraud_status', 'fraud')->count();
        $notFraudCount = Vehicle::where('fraud_status', 'not_fraud')->count();

        return view('admin.fraud', compact(
            'pendingVehicles',
            'fraudVehicles',
            'notFraudVehicles',
            'approvedCount',
            'rejectedCount',
            'totalCount',
            'fraudCount',
            'notFraudCount'
        ));
    }

    // ══════════════════════════════════════════════════════════
    // ANALYZE SINGLE VEHICLE FOR FRAUD
    // ══════════════════════════════════════════════════════════
    public function analyzeVehicle(Request $request, $id)
    {
        $vehicle = Vehicle::with('user')->findOrFail($id);

        $result = $this->runFraudChecks($vehicle);

        // Save result to DB
        $vehicle->fraud_status = $result['is_fraud'] ? 'fraud' : 'not_fraud';
        $vehicle->fraud_score  = $result['score'];
        $vehicle->fraud_reasons = json_encode($result['reasons']);
        $vehicle->save();

        return response()->json([
            'success'   => true,
            'is_fraud'  => $result['is_fraud'],
            'score'     => $result['score'],
            'reasons'   => $result['reasons'],
            'flags'     => $result['flags'],
            'label'     => $result['is_fraud'] ? 'FRAUD' : 'NOT FRAUD',
            'message'   => $result['is_fraud']
                ? "⚠️ Fraud detected! Score: {$result['score']}% (below threshold of " . self::FRAUD_THRESHOLD . "%)"
                : "✅ Vehicle looks legitimate. Score: {$result['score']}% (above threshold of " . self::FRAUD_THRESHOLD . "%)",
        ]);
    }

    // ══════════════════════════════════════════════════════════
    // ANALYZE ALL PENDING VEHICLES AT ONCE
    // ══════════════════════════════════════════════════════════
    public function analyzeAllPending()
    {
        $pendingVehicles = Vehicle::where('status', 'pending')
            ->whereNull('fraud_status')
            ->with('user')
            ->get();

        $analyzed = 0;
        $fraudFound = 0;

        foreach ($pendingVehicles as $vehicle) {
            $result = $this->runFraudChecks($vehicle);
            $vehicle->fraud_status  = $result['is_fraud'] ? 'fraud' : 'not_fraud';
            $vehicle->fraud_score   = $result['score'];
            $vehicle->fraud_reasons = json_encode($result['reasons']);
            $vehicle->save();
            $analyzed++;
            if ($result['is_fraud']) $fraudFound++;
        }

        return response()->json([
            'success'     => true,
            'analyzed'    => $analyzed,
            'fraud_found' => $fraudFound,
            'message'     => "Analyzed {$analyzed} vehicles. {$fraudFound} flagged as fraud.",
        ]);
    }

    // ══════════════════════════════════════════════════════════
    // EXTRACT SMARTCARD DATA VIA AI (Gemini/Groq Vision)
    // ══════════════════════════════════════════════════════════
    public function extractAndAnalyze(Request $request, $id)
    {
        $vehicle = Vehicle::with('user')->findOrFail($id);

        // Check smartcard image exists
        if (!$vehicle->smartcard_image) {
            return response()->json([
                'success' => false,
                'message' => 'No smartcard image found for this vehicle.',
            ]);
        }

        $smartcardPath = public_path('uploads/smartcards/' . $vehicle->smartcard_image);
        if (!file_exists($smartcardPath)) {
            return response()->json([
                'success' => false,
                'message' => 'Smartcard image file not found on server.',
            ]);
        }

        // Extract data from smartcard image via AI
        $imageData = base64_encode(file_get_contents($smartcardPath));
        $mimeType  = mime_content_type($smartcardPath);

        $extractionPrompt = "You are a vehicle document parser for Pakistan. Analyze this vehicle registration document / smart card image carefully.\n\nExtract ALL visible fields. Return ONLY valid JSON with no markdown, no explanation, no extra text:\n{\n  \"vehicle_number\": \"registration number like ABC-123 or ABC-1234 or LHR-1234\",\n  \"chassis_number\": \"chassis/VIN number (alphanumeric string)\",\n  \"engine_number\": \"engine number if visible\",\n  \"vehicle_type\": \"type like Truck, Mini Truck, Pickup, Van, Mazda, Loader, etc\",\n  \"make\": \"vehicle brand/manufacturer\",\n  \"model\": \"vehicle model name\",\n  \"year\": \"manufacturing or registration year (4 digits)\",\n  \"color\": \"vehicle color\",\n  \"owner_name\": \"registered owner full name\",\n  \"confidence\": \"high/medium/low\"\n}\n\nIf any field is not clearly visible, set it to null. Return ONLY the JSON object, nothing else.";

        // Try Gemini Vision first
        $geminiResult = $this->callGeminiVision($imageData, $mimeType, $extractionPrompt);
        $aiResult = $geminiResult;

        if (!$geminiResult['success']) {
            Log::info("FraudController: Gemini failed, trying Groq Vision for vehicle #{$id}");
            $aiResult = $this->callGroqVision($imageData, $mimeType, $extractionPrompt);
        }

        if (!$aiResult['success']) {
            // Fallback: run fraud checks without AI extraction
            Log::warning("FraudController: AI extraction failed for vehicle #{$id}, running basic checks");
            $result = $this->runFraudChecks($vehicle, null);
            $vehicle->fraud_status  = $result['is_fraud'] ? 'fraud' : 'not_fraud';
            $vehicle->fraud_score   = $result['score'];
            $vehicle->fraud_reasons = json_encode($result['reasons']);
            $vehicle->save();

            return response()->json([
                'success'        => true,
                'is_fraud'       => $result['is_fraud'],
                'score'          => $result['score'],
                'reasons'        => $result['reasons'],
                'flags'          => $result['flags'],
                'extracted_data' => null,
                'ai_used'        => false,
                'label'          => $result['is_fraud'] ? 'FRAUD' : 'NOT FRAUD',
                'message'        => 'AI extraction failed. Basic fraud checks applied.',
            ]);
        }

        // Parse AI JSON response
        $jsonText = $aiResult['text'];
        $jsonText = preg_replace('/```json\s*/i', '', $jsonText);
        $jsonText = preg_replace('/```\s*/i', '', $jsonText);
        if (preg_match('/\{[\s\S]*?\}/s', $jsonText, $matches)) {
            $jsonText = $matches[0];
        }
        $extractedData = json_decode(trim($jsonText), true);

        if (!$extractedData) {
            Log::warning("FraudController: JSON parse failed for vehicle #{$id}. Raw: {$jsonText}");
            $extractedData = null;
        }

        // Run comprehensive fraud checks with extracted data
        $result = $this->runFraudChecks($vehicle, $extractedData);

        // Save to DB
        $vehicle->fraud_status       = $result['is_fraud'] ? 'fraud' : 'not_fraud';
        $vehicle->fraud_score        = $result['score'];
        $vehicle->fraud_reasons      = json_encode($result['reasons']);
        $vehicle->smartcard_extracted = json_encode($extractedData);
        $vehicle->save();

        return response()->json([
            'success'        => true,
            'is_fraud'       => $result['is_fraud'],
            'score'          => $result['score'],
            'reasons'        => $result['reasons'],
            'flags'          => $result['flags'],
            'extracted_data' => $extractedData,
            'ai_used'        => true,
            'provider'       => $aiResult['provider'] ?? 'unknown',
            'label'          => $result['is_fraud'] ? 'FRAUD' : 'NOT FRAUD',
            'message'        => $result['is_fraud']
                ? "⚠️ Fraud detected! Score: {$result['score']}%"
                : "✅ Vehicle appears legitimate. Score: {$result['score']}%",
        ]);
    }

    // ══════════════════════════════════════════════════════════
    // CORE FRAUD DETECTION ENGINE
    // ══════════════════════════════════════════════════════════
    private function runFraudChecks(Vehicle $vehicle, ?array $extractedData = null): array
    {
        $score   = 0;
        $reasons = [];
        $flags   = [];

        // ── 1. CHASSIS NUMBER CHECK (50 points) ──────────────
        $chassisScore = 0;
        if ($extractedData && !empty($extractedData['chassis_number']) && !empty($vehicle->chassis_number)) {
            $extractedChassis = strtoupper(preg_replace('/[^A-Z0-9]/i', '', $extractedData['chassis_number']));
            $inputChassis     = strtoupper(preg_replace('/[^A-Z0-9]/i', '', $vehicle->chassis_number));

            if ($extractedChassis === $inputChassis) {
                $chassisScore = self::WEIGHT_CHASSIS; // 50 points — full match
                $reasons[]    = "✅ Chassis number matches smartcard ({$inputChassis})";
            } else {
                $chassisScore = 0; // MUST match — 0 if different
                $reasons[]    = "❌ CHASSIS MISMATCH: Entered [{$inputChassis}] vs Smartcard [{$extractedChassis}]";
                $flags[]      = 'chassis_mismatch';
            }
        } elseif (!$extractedData) {
            // No AI data — give partial credit if chassis exists
            $chassisScore = !empty($vehicle->chassis_number) ? 25 : 0;
            $reasons[]    = $chassisScore > 0
                ? "⚠️ Chassis number present but smartcard not verified (no AI extraction)"
                : "❌ No chassis number provided";
        } else {
            // AI ran but couldn't read chassis from image
            $chassisScore = 15;
            $reasons[]    = "⚠️ Chassis number not readable from smartcard image";
        }
        $score += $chassisScore;

        // ── 2. VEHICLE NUMBER / REGNO CHECK (30 points) ──────
        $vehicleNoScore = 0;
        if ($extractedData && !empty($extractedData['vehicle_number']) && !empty($vehicle->vehicle_number)) {
            $extractedNo = strtoupper(preg_replace('/[\s\-]/i', '', $extractedData['vehicle_number']));
            $inputNo     = strtoupper(preg_replace('/[\s\-]/i', '', $vehicle->vehicle_number));

            similar_text($extractedNo, $inputNo, $similarity);

            if ($similarity >= 90) {
                $vehicleNoScore = self::WEIGHT_VEHICLE_NO; // 30 points
                $reasons[]      = "✅ Vehicle registration number matches ({$inputNo})";
            } elseif ($similarity >= 70) {
                $vehicleNoScore = 15;
                $reasons[]      = "⚠️ Vehicle number partial match ({$similarity}% similar): [{$inputNo}] vs [{$extractedNo}]";
                $flags[]        = 'vehicle_number_partial';
            } else {
                $vehicleNoScore = 0;
                $reasons[]      = "❌ Vehicle number mismatch: Entered [{$inputNo}] vs Smartcard [{$extractedNo}]";
                $flags[]        = 'vehicle_number_mismatch';
            }
        } elseif (!$extractedData) {
            // Check for duplicate vehicle number in system
            $duplicate = Vehicle::where('vehicle_number', $vehicle->vehicle_number)
                ->where('id', '!=', $vehicle->id)
                ->where('status', '!=', 'rejected')
                ->exists();

            if ($duplicate) {
                $vehicleNoScore = 0;
                $reasons[]      = "❌ DUPLICATE: Vehicle number {$vehicle->vehicle_number} already registered in system";
                $flags[]        = 'duplicate_vehicle_number';
            } else {
                $vehicleNoScore = 15;
                $reasons[]      = "⚠️ Vehicle number not verified against smartcard";
            }
        } else {
            $vehicleNoScore = 10;
            $reasons[]      = "⚠️ Vehicle registration number not readable from smartcard";
        }
        $score += $vehicleNoScore;

        // ── 3. IMAGE QUALITY CHECK (10 points) ───────────────
        $imageScore = 0;
        if ($vehicle->smartcard_image) {
            $imagePath = public_path('uploads/smartcards/' . $vehicle->smartcard_image);
            if (file_exists($imagePath)) {
                $fileSize = filesize($imagePath); // bytes
                // Min threshold: 50KB for acceptable image quality
                if ($fileSize >= 100 * 1024) {
                    // High quality image (>=100KB)
                    $imageScore = self::WEIGHT_IMAGE_QUALITY; // 10 points
                    $reasons[]  = "✅ Smartcard image quality: Good (" . round($fileSize / 1024) . " KB)";
                } elseif ($fileSize >= 50 * 1024) {
                    $imageScore = 6;
                    $reasons[]  = "⚠️ Smartcard image quality: Acceptable (" . round($fileSize / 1024) . " KB)";
                } else {
                    $imageScore = 0;
                    $reasons[]  = "❌ Smartcard image too small/low quality (" . round($fileSize / 1024) . " KB < 50KB threshold)";
                    $flags[]    = 'low_quality_image';
                }

                // Blur check via AI confidence
                if ($extractedData && isset($extractedData['confidence'])) {
                    if ($extractedData['confidence'] === 'low') {
                        $imageScore = max(0, $imageScore - 5);
                        $reasons[]  = "⚠️ AI detected low confidence reading smartcard (possibly blurry)";
                        $flags[]    = 'blur_detected';
                    }
                }
            } else {
                $imageScore = 0;
                $reasons[]  = "❌ Smartcard image file missing from server";
                $flags[]    = 'image_missing';
            }
        } else {
            $imageScore = 0;
            $reasons[]  = "❌ No smartcard image uploaded";
            $flags[]    = 'no_smartcard_image';
        }
        $score += $imageScore;

        // ── 4. OWNER NAME CHECK (5 points) ───────────────────
        $ownerScore = 0;
        if ($extractedData && !empty($extractedData['owner_name']) && $vehicle->user) {
            $extractedName = strtolower(trim($extractedData['owner_name']));
            $providerName  = strtolower(trim($vehicle->user->name ?? ''));

            similar_text($extractedName, $providerName, $nameSimilarity);

            if ($nameSimilarity >= 70) {
                $ownerScore = self::WEIGHT_OWNER_NAME; // 5 points
                $reasons[]  = "✅ Owner name matches provider ({$nameSimilarity}% similar)";
            } elseif ($nameSimilarity >= 40) {
                $ownerScore = 3;
                $reasons[]  = "⚠️ Owner name partially matches ({$nameSimilarity}% similar)";
            } else {
                $ownerScore = 0;
                $reasons[]  = "❌ Owner name mismatch: Smartcard [{$extractedData['owner_name']}] vs Provider [{$vehicle->user->name}]";
                $flags[]    = 'owner_name_mismatch';
            }
        } else {
            $ownerScore = 3; // Neutral if not available
            $reasons[]  = "⚠️ Owner name not verified (not readable or no provider info)";
        }
        $score += $ownerScore;

        // ── 5. VEHICLE TYPE CHECK (5 points) ─────────────────
        $typeScore = 0;
        if ($extractedData && !empty($extractedData['vehicle_type']) && !empty($vehicle->vehicle_type)) {
            $extractedType = strtolower($extractedData['vehicle_type']);
            $inputType     = strtolower($vehicle->vehicle_type);

            // Keyword matching for type
            $typeMap = [
                'truck'      => ['truck', 'lorry', 'hino', 'faw', 'shehzore'],
                'mini truck' => ['mini truck', 'mini', 'small truck'],
                'pickup'     => ['pickup', 'pick up', 'pick-up', 'hilux', 'ravi'],
                'van'        => ['van', 'hiace', 'coaster', 'minivan'],
                'loader'     => ['loader', 'loading'],
                'mazda'      => ['mazda', 'titan'],
                'trailer'    => ['trailer', 'semi'],
                'bike'       => ['bike', 'motorcycle', 'motor cycle', 'motorbike'],
            ];

            $typeMatched = false;
            foreach ($typeMap as $category => $keywords) {
                $inputInCategory    = collect($keywords)->contains(fn($k) => str_contains($inputType, $k));
                $extractedInCategory = collect($keywords)->contains(fn($k) => str_contains($extractedType, $k));
                if ($inputInCategory && $extractedInCategory) {
                    $typeMatched = true;
                    break;
                }
            }

            if ($typeMatched || similar_text($extractedType, $inputType) >= 70) {
                $typeScore = self::WEIGHT_VEHICLE_TYPE; // 5 points
                $reasons[] = "✅ Vehicle type matches ({$vehicle->vehicle_type})";
            } else {
                $typeScore = 0;
                $reasons[] = "❌ Vehicle type mismatch: Entered [{$vehicle->vehicle_type}] vs Smartcard [{$extractedData['vehicle_type']}]";
                $flags[]   = 'vehicle_type_mismatch';
            }
        } else {
            $typeScore = 3;
            $reasons[] = "⚠️ Vehicle type not verified against smartcard";
        }
        $score += $typeScore;

        // ── 6. HARD RULE: Bike with >50 ton capacity → Fraud ─
        $vehicleTypeLower = strtolower($vehicle->vehicle_type ?? '');
        if (
            (str_contains($vehicleTypeLower, 'bike') || str_contains($vehicleTypeLower, 'motorcycle')) &&
            (float)($vehicle->weight_capacity ?? 0) > 50000
        ) {
            $score    = 0;
            $reasons[] = "❌ CRITICAL: Bike/Motorcycle cannot have >50,000 kg capacity — Invalid vehicle data";
            $flags[]   = 'invalid_bike_capacity';
        }

        // ── 7. DUPLICATE CNIC CHECK ───────────────────────────
        if ($vehicle->user && $vehicle->user->cnic) {
            $duplicateCnic = Userr::where('cnic', $vehicle->user->cnic)
                ->where('id', '!=', $vehicle->user_id)
                ->exists();
            if ($duplicateCnic) {
                $score    = max(0, $score - 15);
                $reasons[] = "❌ DUPLICATE CNIC: Same CNIC already registered with another account";
                $flags[]   = 'duplicate_cnic';
            }
        }

        // ── 8. DUPLICATE VEHICLE NUMBER CHECK ────────────────
        $duplicateVehicle = Vehicle::where('vehicle_number', $vehicle->vehicle_number)
            ->where('id', '!=', $vehicle->id)
            ->where('status', '!=', 'rejected')
            ->exists();
        if ($duplicateVehicle) {
            $score    = max(0, $score - 20);
            $reasons[] = "❌ DUPLICATE: Vehicle number {$vehicle->vehicle_number} already exists in the system";
            $flags[]   = 'duplicate_vehicle_number';
        }

        // ── 9. DUPLICATE CHASSIS CHECK ───────────────────────
        if (!empty($vehicle->chassis_number)) {
            $duplicateChassis = Vehicle::where('chassis_number', $vehicle->chassis_number)
                ->where('id', '!=', $vehicle->id)
                ->where('status', '!=', 'rejected')
                ->exists();
            if ($duplicateChassis) {
                $score    = 0; // Chassis duplicate is critical
                $reasons[] = "❌ CRITICAL DUPLICATE: Chassis number {$vehicle->chassis_number} already registered";
                $flags[]   = 'duplicate_chassis';
            }
        }

        $score    = max(0, min(100, $score));
        $isFraud  = $score < self::FRAUD_THRESHOLD; // < 60 → Fraud

        return [
            'score'    => $score,
            'is_fraud' => $isFraud,
            'reasons'  => $reasons,
            'flags'    => $flags,
        ];
    }

    // ══════════════════════════════════════════════════════════
    // MARK FRAUD / NOT FRAUD MANUALLY
    // ══════════════════════════════════════════════════════════
    public function markFraud(Request $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $vehicle->fraud_status = 'fraud';
        $vehicle->save();

        return response()->json(['success' => true, 'message' => 'Vehicle marked as FRAUD.']);
    }

    public function markNotFraud(Request $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $vehicle->fraud_status = 'not_fraud';
        $vehicle->save();

        return response()->json(['success' => true, 'message' => 'Vehicle marked as NOT FRAUD.']);
    }

    // ══════════════════════════════════════════════════════════
    // GEMINI VISION (same pattern as ProviderChatbotController)
    // ══════════════════════════════════════════════════════════
    protected function callGeminiVision(string $imageBase64, string $mimeType, string $prompt): array
    {
        $apiKeys = [];
        foreach (['GEMINI_API_KEY_1', 'GEMINI_API_KEY_2', 'GEMINI_API_KEY_3', 'GEMINI_API_KEY_4'] as $envKey) {
            $val = env($envKey);
            if ($val && strlen(trim($val)) > 10) $apiKeys[] = trim($val);
        }
        $legacy = env('GEMINI_API_KEY');
        if ($legacy && !in_array(trim($legacy), $apiKeys)) array_unshift($apiKeys, trim($legacy));
        $apiKeys = array_unique($apiKeys);

        if (empty($apiKeys)) {
            return ['success' => false, 'error' => 'No Gemini API keys configured', 'provider' => 'gemini'];
        }

        $visionModels = ['gemini-2.0-flash', 'gemini-1.5-flash', 'gemini-2.0-flash-lite', 'gemini-1.5-flash-8b'];

        foreach ($apiKeys as $keyIndex => $apiKey) {
            $keySlot = 'gemini_key_quota_' . md5($apiKey);
            if (Cache::has($keySlot)) continue;

            foreach ($visionModels as $model) {
                try {
                    $url  = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";
                    $body = [
                        'contents' => [['role' => 'user', 'parts' => [
                            ['inline_data' => ['mime_type' => $mimeType, 'data' => $imageBase64]],
                            ['text' => $prompt],
                        ]]],
                        'generationConfig' => ['temperature' => 0.1, 'maxOutputTokens' => 600],
                        'safetySettings'   => [
                            ['category' => 'HARM_CATEGORY_HARASSMENT',        'threshold' => 'BLOCK_NONE'],
                            ['category' => 'HARM_CATEGORY_HATE_SPEECH',       'threshold' => 'BLOCK_NONE'],
                            ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_NONE'],
                            ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_NONE'],
                        ],
                    ];

                    $response = Http::timeout(35)->withHeaders(['Content-Type' => 'application/json'])->post($url, $body);
                    $status   = $response->status();

                    if ($response->successful()) {
                        $text = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? null;
                        if ($text && strlen(trim($text)) > 5) {
                            Log::info("FraudController GeminiVision: success key#{$keyIndex} model:{$model}");
                            return ['success' => true, 'text' => trim($text), 'provider' => 'gemini'];
                        }
                        continue;
                    }

                    if ($status === 429 || $status === 403) {
                        $nowPKT   = now()->timezone('Asia/Karachi');
                        $midnight = $nowPKT->copy()->addDay()->startOfDay();
                        Cache::put($keySlot, true, max($nowPKT->diffInSeconds($midnight), 60));
                        break;
                    }
                    if ($status === 404) continue;

                } catch (\Exception $e) {
                    Log::warning("FraudController GeminiVision exception: " . $e->getMessage());
                    continue;
                }
            }
        }

        return ['success' => false, 'error' => 'All Gemini Vision attempts failed', 'provider' => 'gemini'];
    }

    // ══════════════════════════════════════════════════════════
    // GROQ VISION FALLBACK
    // ══════════════════════════════════════════════════════════
    protected function callGroqVision(string $imageBase64, string $mimeType, string $prompt): array
    {
        $groqKey = env('GROQ_API_KEY');
        if (!$groqKey) return ['success' => false, 'error' => 'No Groq API key', 'provider' => 'groq'];

        $visionModels = [
            'meta-llama/llama-4-scout-17b-16e-instruct',
            'meta-llama/llama-4-maverick-17b-128e-instruct',
            'llama-3.2-90b-vision-preview',
        ];

        foreach ($visionModels as $model) {
            $permanentBan = 'groq_vision_dead_' . md5($groqKey . $model);
            $slotKey      = 'groq_vision_quota_' . md5($groqKey . $model);
            if (Cache::has($permanentBan) || Cache::has($slotKey)) continue;

            try {
                $response = Http::timeout(45)
                    ->withHeaders(['Authorization' => 'Bearer ' . $groqKey, 'Content-Type' => 'application/json'])
                    ->post('https://api.groq.com/openai/v1/chat/completions', [
                        'model'       => $model,
                        'max_tokens'  => 700,
                        'temperature' => 0.1,
                        'messages'    => [['role' => 'user', 'content' => [
                            ['type' => 'image_url', 'image_url' => ['url' => "data:{$mimeType};base64,{$imageBase64}", 'detail' => 'high']],
                            ['type' => 'text', 'text' => $prompt],
                        ]]],
                    ]);

                $status = $response->status();

                if ($response->successful()) {
                    $text = $response->json()['choices'][0]['message']['content'] ?? null;
                    if ($text && strlen(trim($text)) > 5) {
                        Log::info("FraudController GroqVision: success model:{$model}");
                        return ['success' => true, 'text' => trim($text), 'provider' => 'groq'];
                    }
                    continue;
                }

                if ($status === 429) {
                    Cache::put($slotKey, true, 60);
                    continue;
                }

                if ($status === 400) {
                    $errMsg = $response->json()['error']['message'] ?? '';
                    if (str_contains($errMsg, 'decommissioned') || str_contains($errMsg, 'deprecated')) {
                        Cache::put($permanentBan, true, now()->addDays(30));
                    }
                    continue;
                }

            } catch (\Exception $e) {
                Log::warning("FraudController GroqVision exception on {$model}: " . $e->getMessage());
                continue;
            }
        }

        return ['success' => false, 'error' => 'All Groq Vision attempts failed', 'provider' => 'groq'];
    }
}