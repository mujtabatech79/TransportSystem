<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GeminiService;
use App\Models\Vehicle;
use App\Models\Booking;
use App\Models\Complaint;
use App\Models\Review;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    protected $gemini;

    // ── Language Modes ────────────────────────────────────────
    const LANG_ENGLISH    = 'english';
    const LANG_URDU       = 'urdu';       // Urdu script (اردو)
    const LANG_ROMAN_URDU = 'roman_urdu'; // Roman Urdu (kya hai, batao)

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    // ══════════════════════════════════════════════════════════
    // PROCESS MESSAGE
    // ══════════════════════════════════════════════════════════

    public function processMessage(Request $request)
    {
        $request->validate(['message' => 'required|string|max:2000']);

        $message    = trim($request->message);
        $customerId = session('user_id');

        if (!$customerId) {
            return response()->json([
                'success'  => false,
                'response' => 'Please log in to use the AI assistant.',
            ]);
        }

        if (strlen($message) < 2) {
            return response()->json([
                'success'  => true,
                'response' => 'Thoda detail mein batayein ya type more to help you! 😊',
            ]);
        }

        // Detect exact language mode
        $lang    = $this->detectLanguage($message);
        $context = $this->getContext($customerId);
        $history = $this->getRecentHistory($customerId, 6);

        $systemPrompt = $this->buildSystemPrompt($context, $lang);
        $userPrompt   = $this->buildUserPrompt($message, $context, $lang, $history);

        $aiResponse = null;

        try {
            $aiResponse = $this->gemini->generate($userPrompt, $systemPrompt);
        } catch (\Exception $e) {
            Log::error('AI exception: ' . $e->getMessage());
        }

        // Smart NLP fallback if AI unavailable
        if (!$aiResponse || strlen(trim($aiResponse)) < 5) {
            $aiResponse = $this->smartFallback($message, $context, $lang);
        }

        $this->saveChat($customerId, 'user', $message);
        $this->saveChat($customerId, 'bot',  $aiResponse);

        return response()->json([
            'success'     => true,
            'response'    => $aiResponse,
            'suggestions' => $this->getSuggestions($message, $context, $lang),
            'lang'        => $lang, // for debugging
        ]);
    }

    // ══════════════════════════════════════════════════════════
    // LANGUAGE DETECTION — Three-way: English / Urdu / Roman Urdu
    // ══════════════════════════════════════════════════════════

    private function detectLanguage(string $message): string
    {
        // 1. Urdu script — highest priority
        if (preg_match('/[\x{0600}-\x{06FF}]/u', $message)) {
            return self::LANG_URDU;
        }

        $lower = mb_strtolower(trim($message));

        // 2. Strong Roman Urdu phrases — exact matches
        $strongRomanPhrases = [
            'kya hai', 'kya hain', 'kya ho', 'kya kr', 'kya kar',
            'kaise hai', 'kaisi hai', 'kaise hain', 'kesy hai',
            'mujhe batao', 'mujhe btao', 'mujhe chahiye', 'mujhe bta',
            'ap ki', 'aap ki', 'meri booking', 'mera booking',
            'koi vehicle', 'dastiyab', 'kiraya', 'kitna hai', 'kitne hain',
            'truck wala', 'gaadi', 'kahan hai', 'kab tak', 'kab hoga',
            'booking kaise', 'kaise book', 'book karna', 'book krna',
            'nahi hai', 'nhi hai', 'paise', 'masla hai', 'masla kya',
            'shikayat', 'complaint hai', 'help chahiye', 'samjhao',
            'dikhao', 'batao', 'btao', 'bata do', 'bta do',
            'theek hai', 'thk hai', 'shukriya', 'shukriya',
            'bahut acha', 'bahut achha', 'zyada',
        ];

        foreach ($strongRomanPhrases as $phrase) {
            if (str_contains($lower, $phrase)) {
                return self::LANG_ROMAN_URDU;
            }
        }

        // 3. Roman Urdu keyword scoring
        $romanKeywords = [
            // Common words
            'hai'  => 2, 'hain' => 2, 'ho'   => 1, 'tha'  => 2, 'thi'  => 2,
            'hoga' => 2, 'hogi' => 2, 'hote' => 2, 'hoti' => 2,
            // Pronouns
            'main' => 2, 'mein' => 1, 'mera' => 2, 'meri' => 2, 'mujhe'=> 2,
            'ap'   => 1, 'aap'  => 2, 'uska' => 2, 'uski' => 2, 'unka' => 2,
            'yeh'  => 2, 'woh'  => 2, 'jo'   => 1, 'jis'  => 1,
            // Question words
            'kya'  => 3, 'kaise'=> 3, 'kesy' => 3, 'kyun' => 3,
            'kab'  => 3, 'kahan'=> 3, 'kitna'=> 3, 'kitni'=> 3, 'kitne'=> 3,
            'konsa'=> 3, 'konsi'=> 3,
            // Actions
            'karo' => 2, 'kren' => 2, 'krna' => 2, 'karna'=> 2,
            'dena' => 2, 'lena' => 2, 'dena' => 2, 'btao' => 3,
            'batao'=> 3, 'dikhao'=>3, 'dekho'=> 2, 'samjhao'=>3,
            'chahiye'=>3,'chahta'=>2,'chahti'=>2,
            // Connectors / particles
            'aur'  => 1, 'bhi'  => 1, 'toh'  => 1, 'lekin'=> 2,
            'phir' => 1, 'agar' => 2, 'kyunke'=>2, 'matlab'=> 2,
            'ke'   => 1, 'ka'   => 1, 'ki'   => 1, 'ko'   => 1,
            'se'   => 1, 'pe'   => 1, 'par'  => 1, 'ne'   => 1,
            // TruckLink-specific
            'booking'  => 1, 'kiraya'   => 3, 'dastiyab' => 3,
            'gaadi'    => 3, 'truck'    => 1, 'masla'    => 3,
            'shikayat' => 3, 'paise'    => 3, 'payment'  => 1,
            'abhi'     => 2, 'theek'    => 2, 'accha'    => 2,
            'achha'    => 2, 'salam'    => 2, 'assalam'  => 2,
            'nahi'     => 2, 'nhi'      => 2, 'bilkul'   => 2,
            'zaroor'   => 2, 'please'   => 0, // neutral
        ];

        // Tokenize by spaces
        $words = preg_split('/\s+/', $lower);
        $score = 0;
        $matched = 0;

        foreach ($words as $word) {
            $word = preg_replace('/[^a-z]/', '', $word); // strip punctuation
            if (isset($romanKeywords[$word])) {
                $score  += $romanKeywords[$word];
                $matched++;
            }
        }

        // Threshold: score ≥ 4 OR ≥ 2 keyword matches
        if ($score >= 4 || $matched >= 2) {
            return self::LANG_ROMAN_URDU;
        }

        // 4. Default → English
        return self::LANG_ENGLISH;
    }

    // Helper: is message non-English (Urdu OR Roman Urdu)?
    private function isUrduFamily(string $lang): bool
    {
        return in_array($lang, [self::LANG_URDU, self::LANG_ROMAN_URDU]);
    }

    // ══════════════════════════════════════════════════════════
    // GET CHAT HISTORY
    // ══════════════════════════════════════════════════════════

    public function getChatHistory()
    {
        $customerId = session('user_id');
        if (!$customerId) {
            return response()->json(['success' => false, 'history' => []]);
        }

        $history = DB::table('chat_histories')
            ->where('customer_id', $customerId)
            ->orderBy('created_at', 'asc')
            ->limit(60)
            ->get(['sender', 'message', 'created_at']);

        return response()->json(['success' => true, 'history' => $history]);
    }

    // ══════════════════════════════════════════════════════════
    // CLEAR HISTORY
    // ══════════════════════════════════════════════════════════

    public function clearHistory()
    {
        $customerId = session('user_id');
        if ($customerId) {
            DB::table('chat_histories')->where('customer_id', $customerId)->delete();
            Cache::forget("chatbot_context_{$customerId}");
        }
        return response()->json(['success' => true]);
    }

    // ══════════════════════════════════════════════════════════
    // TEST CONNECTION
    // ══════════════════════════════════════════════════════════

    public function testConnection()
    {
        return response()->json($this->gemini->testConnection());
    }

    // ══════════════════════════════════════════════════════════
    // CONTEXT BUILDER
    // ══════════════════════════════════════════════════════════

    private function getContext(int $customerId): array
    {
        return Cache::remember("chatbot_context_{$customerId}", now()->addMinutes(3), function () use ($customerId) {
            return $this->buildContext($customerId);
        });
    }

    private function buildContext(int $customerId): array
    {
        $vehicles = Vehicle::with('user')->get();

        $vehicleList = $vehicles->map(function ($v) {
            $activeBooking = null;
            if ($v->is_booked === 'yes') {
                $activeBooking = Booking::where('vehicle_id', $v->id)->where('status', 'accept')->latest()->first();
            }
            $avgRating = Review::whereHas('booking', fn($q) => $q->where('vehicle_id', $v->id))->avg('rating');

            return [
                'id'               => $v->id,
                'type'             => $v->vehicle_type    ?? 'Unknown',
                'number'           => $v->vehicle_number  ?? 'N/A',
                'capacity_kg'      => $v->weight_capacity ?? 0,
                'can_carry'        => $v->can_carry        ?? 'General goods',
                'is_active'        => (bool) $v->is_active,
                'is_booked'        => $v->is_booked === 'yes',
                'provider'         => $v->user?->name     ?? 'N/A',
                'provider_contact' => $v->user?->mobile   ?? 'N/A',
                'delivery_status'  => $activeBooking?->delivery_status ?? null,
                'est_duration'     => $activeBooking
                    ? ($activeBooking->duration_text ?? ($activeBooking->estimated_duration ? $activeBooking->estimated_duration . ' min' : null))
                    : null,
                'avg_rating'       => $avgRating ? round($avgRating, 1) : null,
            ];
        })->values()->toArray();

        $topRated = collect($vehicleList)
            ->filter(fn($v) => $v['is_active'] && $v['avg_rating'] !== null)
            ->sortByDesc('avg_rating')->take(5)->values()->toArray();

        $allBookings    = Booking::where('customer_id', $customerId)->with('vehicle')->get();
        $activeBookings = $allBookings->where('status', 'accept')->values();

        $activeDetails = $activeBookings->map(fn($b) => [
            'id'              => $b->id,
            'from'            => $b->pickup_location,
            'to'              => $b->dropoff_location,
            'delivery_status' => $b->delivery_status ?? 'order_confirmed',
            'vehicle_type'    => $b->vehicle?->vehicle_type   ?? 'N/A',
            'vehicle_number'  => $b->vehicle?->vehicle_number ?? 'N/A',
            'est_duration'    => $b->duration_text ?? ($b->estimated_duration ? $b->estimated_duration . ' min' : 'N/A'),
            'est_fare'        => $b->estimated_fare ?? 0,
            'goods_type'      => $b->goods_type    ?? 'N/A',
            'goods_weight'    => $b->goods_weight   ?? 0,
        ])->values()->toArray();

        $recentBookings = $allBookings->sortByDesc('created_at')->take(5)->map(fn($b) => [
            'id'               => $b->id,
            'from'             => $b->pickup_location,
            'to'               => $b->dropoff_location,
            'status'           => $b->status,
            'status_text'      => $b->status_text ?? $b->status,
            'goods_type'       => $b->goods_type   ?? 'N/A',
            'goods_weight'     => $b->goods_weight ?? 0,
            'est_fare'         => $b->estimated_fare ?? 0,
            'actual_fare'      => $b->actual_fare    ?? 0,
            'payment_status'   => $b->payment_status ?? 'pending',
            'date'             => $b->booking_date ? $b->booking_date->format('d M Y') : 'N/A',
            'vehicle_type'     => $b->vehicle?->vehicle_type   ?? 'N/A',
            'vehicle_number'   => $b->vehicle?->vehicle_number ?? 'N/A',
            'rejection_reason' => $b->rejection_reason ?? null,
            'penalty_amount'   => $b->penalty_amount  ?? 0,
        ])->values()->toArray();

        $complaints = Complaint::where('customer_id', $customerId)->get();
        $recentComplaints = $complaints->sortByDesc('created_at')->take(5)->map(fn($c) => [
            'id'             => $c->id,
            'subject'        => $c->subject        ?? 'N/A',
            'type'           => $c->complaint_type ?? 'N/A',
            'status'         => $c->status,
            'admin_response' => $c->admin_response ?? null,
            'date'           => $c->created_at?->format('d M Y') ?? 'N/A',
        ])->values()->toArray();

        return [
            'total_vehicles'      => count($vehicleList),
            'available_vehicles'  => collect($vehicleList)->where('is_active', true)->where('is_booked', false)->count(),
            'booked_vehicles'     => collect($vehicleList)->where('is_booked', true)->count(),
            'inactive_vehicles'   => collect($vehicleList)->where('is_active', false)->count(),
            'vehicle_list'        => $vehicleList,
            'top_rated'           => $topRated,
            'customer_name'       => session('name', 'Customer'),
            'total_bookings'      => $allBookings->count(),
            'active_count'        => $activeBookings->count(),
            'completed_count'     => $allBookings->where('status', 'complete')->count(),
            'pending_count'       => $allBookings->where('status', 'request')->count(),
            'rejected_count'      => $allBookings->where('status', 'reject')->count(),
            'total_spent'         => (float) $allBookings->where('status', 'complete')->sum('actual_fare'),
            'active_bookings'     => $activeDetails,
            'recent_bookings'     => $recentBookings,
            'total_complaints'    => $complaints->count(),
            'pending_complaints'  => $complaints->where('status', 'pending')->count(),
            'resolved_complaints' => $complaints->where('status', 'resolved')->count(),
            'recent_complaints'   => $recentComplaints,
            'reviews_given'       => Review::where('customer_id', $customerId)->count(),
        ];
    }

    // ══════════════════════════════════════════════════════════
    // RECENT CHAT HISTORY FOR CONTEXT
    // ══════════════════════════════════════════════════════════

    private function getRecentHistory(int $customerId, int $limit = 6): array
    {
        return DB::table('chat_histories')
            ->where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get(['sender', 'message'])
            ->reverse()->values()->toArray();
    }

    // ══════════════════════════════════════════════════════════
    // SYSTEM PROMPT — Trilingual, Professional
    // ══════════════════════════════════════════════════════════

    private function buildSystemPrompt(array $ctx, string $lang): string
    {
        // Language instruction per detected mode
        if ($lang === self::LANG_URDU) {
            $langRule = "USER NE URDU SCRIPT MEIN LIKHA HAI (مثلاً کیا ہے، مجھے بتاؤ)۔ آپ کو لازمی اردو اسکرپٹ میں جواب دینا ہے۔ کبھی انگریزی مت لکھیں جب تک user خود انگریزی استعمال نہ کرے۔";
        } elseif ($lang === self::LANG_ROMAN_URDU) {
            $langRule = "USER NE ROMAN URDU MEIN LIKHA HAI (e.g. 'kya hai', 'mujhe batao', 'booking karo'). Aapko ROMAN URDU mein jawab dena hai — bilkul usi style mein jaise user ne likha. English BILKUL MAT use karo unless user khud English likhey. Example correct reply: 'Jee bilkul! Abhi 3 vehicles dastiyab hain...' Example WRONG reply: 'Sure! Currently 3 vehicles are available...'";
        } else {
            $langRule = "USER WROTE IN ENGLISH. Respond in clear, professional English only. Do NOT mix Urdu or Roman Urdu unless the user does.";
        }

        $p  = "You are TruckLink AI — the intelligent assistant for TruckLink, Pakistan's professional vehicle booking & logistics platform.\n\n";

        $p .= "## ⚠️ CRITICAL LANGUAGE RULE — HIGHEST PRIORITY ⚠️\n";
        $p .= $langRule . "\n\n";
        $p .= "MIRROR the user's language EXACTLY. This rule overrides everything else.\n\n";

        $p .= "## PERSONALITY\n";
        $p .= "- Warm, knowledgeable, like a helpful logistics expert\n";
        $p .= "- Conversational — NOT robotic or overly list-heavy for simple answers\n";
        $p .= "- Proactive — offer next steps naturally\n";
        $p .= "- Max 1-2 emojis per response\n\n";

        $p .= "## DATA RULES\n";
        $p .= "- Max 120 words per response\n";
        $p .= "- ONLY use data from context below — never invent\n";
        $p .= "- ALWAYS use VEHICLE NUMBER (e.g. LEA-1234), never system ID\n";
        $p .= "- If data unavailable, say so and suggest contacting support\n\n";

        $p .= "## TRUCKLINK PLATFORM\n";
        $p .= "- Fare: Rs 100 base + Rs 25/km + Rs 50/30min waiting + tolls\n";
        $p .= "- 50km ≈ Rs 1,350 + tolls\n";
        $p .= "- Booking: Find Vehicles → Select → Book Now → Locations+Goods → Submit (5-10 min confirm)\n";
        $p .= "- Payments: JazzCash, EasyPaisa, Credit/Debit Card, Cash on Delivery\n";
        $p .= "- Late penalty: Rs 200/hour delay (from provider's fare)\n\n";

        // ── Vehicle Data ─────────────────────────────────────
        $p .= "## LIVE VEHICLE DATA\n";
        $p .= "Total:{$ctx['total_vehicles']} | Available:{$ctx['available_vehicles']} | Booked:{$ctx['booked_vehicles']} | Inactive:{$ctx['inactive_vehicles']}\n\n";
        $p .= "Vehicles (always use vehicle number):\n";
        foreach ($ctx['vehicle_list'] as $v) {
            $st = $v['is_booked']
                ? "BOOKED" . ($v['delivery_status'] ? "[{$v['delivery_status']}]" : '') . ($v['est_duration'] ? " ETA:{$v['est_duration']}" : '')
                : ($v['is_active'] ? "AVAILABLE" : "INACTIVE");
            $rt = $v['avg_rating'] ? " ⭐{$v['avg_rating']}" : "";
            $p .= "  [{$v['number']}] {$v['type']} | {$v['capacity_kg']}kg | {$v['can_carry']} | {$st}{$rt} | {$v['provider']} ({$v['provider_contact']})\n";
        }
        if (!empty($ctx['top_rated'])) {
            $p .= "Top Rated:";
            foreach ($ctx['top_rated'] as $v) {
                $p .= " [{$v['number']}]⭐{$v['avg_rating']}/" . ($v['is_booked'] ? 'Booked' : 'Available') . ";";
            }
            $p .= "\n";
        }

        // ── Customer Data ─────────────────────────────────────
        $p .= "\n## CUSTOMER: {$ctx['customer_name']}\n";
        $p .= "Total={$ctx['total_bookings']} Active={$ctx['active_count']} Done={$ctx['completed_count']} Pending={$ctx['pending_count']} Rejected={$ctx['rejected_count']} Spent=Rs" . number_format($ctx['total_spent']) . " Reviews={$ctx['reviews_given']}\n";

        foreach ($ctx['active_bookings'] as $b) {
            $p .= "ACTIVE Booking#{$b['id']}: {$b['from']}→{$b['to']} | {$b['vehicle_number']}({$b['vehicle_type']}) | {$b['delivery_status']} | ETA:{$b['est_duration']} | {$b['goods_type']} {$b['goods_weight']}kg | Rs{$b['est_fare']}\n";
        }
        foreach ($ctx['recent_bookings'] as $b) {
            $pen = $b['penalty_amount'] > 0 ? " Penalty:Rs{$b['penalty_amount']}" : "";
            $rej = $b['rejection_reason'] ? " Reason:{$b['rejection_reason']}" : "";
            $p .= "RECENT Booking#{$b['id']}: {$b['from']}→{$b['to']} | {$b['vehicle_number']} | {$b['status_text']} | Rs{$b['actual_fare']} | {$b['payment_status']} | {$b['date']}{$pen}{$rej}\n";
        }

        // ── Complaints ───────────────────────────────────────
        $p .= "Complaints: Total={$ctx['total_complaints']} Pending={$ctx['pending_complaints']} Resolved={$ctx['resolved_complaints']}\n";
        foreach ($ctx['recent_complaints'] as $c) {
            $resp = $c['admin_response'] ? "AdminReply:{$c['admin_response']}" : "NoReply";
            $p .= "  Complaint#{$c['id']}: {$c['subject']}({$c['type']}) | {$c['status']} | {$resp} | {$c['date']}\n";
        }

        return $p;
    }

    private function buildUserPrompt(string $message, array $ctx, string $lang, array $history): string
    {
        $historyText = '';
        if (!empty($history)) {
            $historyText = "RECENT CONVERSATION:\n";
            foreach ($history as $h) {
                $role = $h->sender === 'user' ? 'Customer' : 'TruckLink AI';
                $historyText .= "{$role}: {$h->message}\n";
            }
            $historyText .= "\n";
        }

        if ($lang === self::LANG_URDU) {
            $langNote = "⚠️ USER NE URDU SCRIPT MEIN LIKHA — صرف اردو اسکرپٹ میں جواب دیں۔";
        } elseif ($lang === self::LANG_ROMAN_URDU) {
            $langNote = "⚠️ USER NE ROMAN URDU MEIN LIKHA — sirf Roman Urdu mein jawab dein (jaise: 'Jee, abhi 3 vehicles dastiyab hain'). English bilkul mat likhein.";
        } else {
            $langNote = "⚠️ USER WROTE IN ENGLISH — reply in English only.";
        }

        return "{$historyText}USER MESSAGE: \"{$message}\"\n\n{$langNote}\nUse ONLY real data from context. Vehicle numbers not IDs. Max 120 words.";
    }

    // ══════════════════════════════════════════════════════════
    // SMART FALLBACK — Trilingual
    // ══════════════════════════════════════════════════════════

    private function smartFallback(string $message, array $ctx, string $lang): string
    {
        $m   = mb_strtolower($message);
        $has = fn(array $words) => collect($words)->contains(fn($w) => str_contains($m, $w));
        $isU = $this->isUrduFamily($lang);       // Urdu OR Roman Urdu
        $isS = ($lang === self::LANG_URDU);       // Urdu Script only

        // ── Response helpers (3 variants each) ────────────────
        $t = function(string $en, string $ru, string $us) use ($lang): string {
            if ($lang === self::LANG_URDU)       return $us;
            if ($lang === self::LANG_ROMAN_URDU) return $ru;
            return $en;
        };

        // ── Greetings ─────────────────────────────────────────
        if ($has(['hi','hello','hey','salam','assalam','walaikum','good morning','good afternoon','good evening','السلام','وعلیکم'])) {
            $greet = $this->getTimeGreeting($lang);
            $en = "👋 {$greet}, **{$ctx['customer_name']}**! Welcome to TruckLink.\n\n**{$ctx['available_vehicles']} vehicles** are available right now. You have **{$ctx['active_count']} active booking(s)**. How can I help you today?";
            $ru = "👋 {$greet}, **{$ctx['customer_name']}** bhai! TruckLink mein khush aamdeed.\n\nAbhi **{$ctx['available_vehicles']} vehicles** dastiyab hain aur aapki **{$ctx['active_count']} active booking(s)** hain. Bataein, kya madad chahiye?";
            $us = "👋 {$greet}! **{$ctx['customer_name']}** صاحب، ٹرک لنک میں خوش آمدید۔\n\nابھی **{$ctx['available_vehicles']} گاڑیاں** دستیاب ہیں اور **{$ctx['active_count']} بکنگ** فعال ہیں۔ کیا مدد چاہیے؟";
            return $t($en, $ru, $us);
        }

        // ── Available Vehicles ───────────────────────────────
        if ($has(['available vehicle','dastiyab','truck available','koi vehicle','which truck','show vehicle','list vehicle','available trucks','konsa truck','gariyan','gaadi dikha','vehicles dikha','dikhaen','dikhao'])) {
            $available = collect($ctx['vehicle_list'])->where('is_active', true)->where('is_booked', false)->values();
            if ($available->isEmpty()) {
                return $t(
                    "😔 No vehicles available right now. Please check back shortly.",
                    "😔 Filhaal koi vehicle dastiyab nahi hai. Thodi der baad dobara check karein.",
                    "😔 ابھی کوئی گاڑی دستیاب نہیں۔ تھوڑی دیر بعد دوبارہ چیک کریں۔"
                );
            }
            $lines = $available->map(fn($v) =>
                "• **{$v['number']}** — {$v['type']} | {$v['capacity_kg']}kg | {$v['can_carry']}" .
                ($v['avg_rating'] ? " | ⭐{$v['avg_rating']}" : "") . " | {$v['provider']}"
            )->toArray();
            $header = $t(
                "🚛 **{$available->count()} Available Vehicles (of {$ctx['total_vehicles']} total):**",
                "🚛 **{$available->count()} Vehicles Dastiyab Hain (Total: {$ctx['total_vehicles']}):**",
                "🚛 **{$available->count()} گاڑیاں دستیاب (کل: {$ctx['total_vehicles']}):**"
            );
            $footer = $t(
                "\n\nGo to **Find Vehicles** to book one.",
                "\n\n**Find Vehicles** section mein jayen book karne ke liye.",
                "\n\n**Find Vehicles** میں جائیں بکنگ کے لیے۔"
            );
            return $header . "\n\n" . implode("\n", $lines) . $footer;
        }

        // ── Top Rated ────────────────────────────────────────
        if ($has(['top rated','best vehicle','best truck','recommended','suggest','konsi gaadi','which vehicle','sabse acha','achha','top gaadi'])) {
            if (empty($ctx['top_rated'])) {
                return $t("⭐ No rated vehicles yet.", "⭐ Abhi kisi vehicle ki review nahi aayi.", "⭐ ابھی کسی گاڑی کی ریٹنگ نہیں آئی۔");
            }
            $lines = [];
            foreach ($ctx['top_rated'] as $i => $v) {
                $st = $v['is_booked']
                    ? $t('🔴 Booked', '🔴 Booked', '🔴 بک ہے')
                    : $t('🟢 Available', '🟢 Available', '🟢 دستیاب');
                $lines[] = ($i+1) . ". **{$v['number']}** — {$v['type']} | ⭐{$v['avg_rating']}/5 | {$v['capacity_kg']}kg | {$v['provider']} | {$st}";
            }
            return "⭐ **" . $t("Top Rated Vehicles:", "Top Rated Vehicles:", "بہترین گاڑیاں:") . "**\n\n" . implode("\n", $lines);
        }

        // ── Active Bookings ──────────────────────────────────
        if ($has(['active booking','current booking','active ha','active hain','meri active','my active','ongoing','chal rahi','active dekho'])) {
            if ($ctx['active_count'] === 0) {
                return $t(
                    "📋 You have no active bookings right now. Want to book a vehicle?",
                    "📋 Filhaal aapki koi active booking nahi hai. Koi vehicle book karein!",
                    "📋 ابھی آپ کی کوئی فعال بکنگ نہیں ہے۔"
                );
            }
            $lines = array_map(fn($b) =>
                "• **Booking #{$b['id']}:** {$b['from']} → {$b['to']}\n" .
                "  🚛 {$b['vehicle_number']} ({$b['vehicle_type']}) | 📦 {$b['goods_type']} ({$b['goods_weight']}kg)\n" .
                "  " . $t("Status:", "Status:", "حالت:") . " **{$b['delivery_status']}** | ETA: {$b['est_duration']} | Rs {$b['est_fare']}",
                $ctx['active_bookings']
            );
            $header = $t(
                "📦 **Your {$ctx['active_count']} Active Booking(s):**",
                "📦 **Aapki {$ctx['active_count']} Active Booking(s):**",
                "📦 **آپ کی {$ctx['active_count']} فعال بکنگ:**"
            );
            return $header . "\n\n" . implode("\n\n", $lines);
        }

        // ── Booking Summary ──────────────────────────────────
        if ($has(['my booking','meri booking','booking history','total booking','booking summary','kitni bookings','how many booking','booking ka hal','bookings dekho','meri sari booking'])) {
            $en = "📊 **Booking Summary ({$ctx['customer_name']}):**\n\n• Total: **{$ctx['total_bookings']}**\n• Active: **{$ctx['active_count']}**\n• Completed: **{$ctx['completed_count']}**\n• Pending: **{$ctx['pending_count']}**\n• Rejected: **{$ctx['rejected_count']}**\n• Total Spent: **Rs " . number_format($ctx['total_spent']) . "**";
            $ru = "📊 **{$ctx['customer_name']} ki Booking Summary:**\n\n• Total: **{$ctx['total_bookings']}**\n• Active: **{$ctx['active_count']}**\n• Complete: **{$ctx['completed_count']}**\n• Pending: **{$ctx['pending_count']}**\n• Reject: **{$ctx['rejected_count']}**\n• Kharch: **Rs " . number_format($ctx['total_spent']) . "**";
            $us = "📊 **{$ctx['customer_name']} کی بکنگ خلاصہ:**\n\n• کل: **{$ctx['total_bookings']}**\n• فعال: **{$ctx['active_count']}**\n• مکمل: **{$ctx['completed_count']}**\n• زیر التوا: **{$ctx['pending_count']}**\n• مسترد: **{$ctx['rejected_count']}**\n• کل خرچ: **Rs " . number_format($ctx['total_spent']) . "**";
            return $t($en, $ru, $us);
        }

        // ── Recent Bookings ──────────────────────────────────
        if ($has(['recent booking','last booking','latest booking','akhri booking','pichli booking','purani booking'])) {
            if (empty($ctx['recent_bookings'])) {
                return $t("📋 No bookings found yet.", "📋 Abhi tak koi booking nahi ki gayi.", "📋 ابھی تک کوئی بکنگ نہیں کی گئی۔");
            }
            $lines = array_map(fn($b) =>
                "• **Booking #{$b['id']}** | {$b['date']}\n" .
                "  📍 {$b['from']} → {$b['to']}\n" .
                "  🚛 {$b['vehicle_number']} ({$b['vehicle_type']}) | **{$b['status_text']}**\n" .
                "  💰 Rs{$b['actual_fare']} | " . $t("Payment","Payment","ادائیگی") . ": {$b['payment_status']}" .
                ($b['penalty_amount'] > 0 ? " | Penalty: Rs{$b['penalty_amount']}" : "") .
                ($b['rejection_reason'] ? "\n  ❌ " . $t("Reason","Wajah","وجہ") . ": {$b['rejection_reason']}" : ""),
                $ctx['recent_bookings']
            );
            $header = $t("📋 **Your Recent Bookings:**", "📋 **Aapki Recent Bookings:**", "📋 **حالیہ بکنگز:**");
            return $header . "\n\n" . implode("\n\n", $lines);
        }

        // ── Tracking ─────────────────────────────────────────
        if ($has(['track','tracking','where is','shipment','kahan hai','kitna time','eta','location','kaahan','pahuncha','delivery kahan'])) {
            if ($ctx['active_count'] === 0) {
                return $t(
                    "📍 No active shipments to track right now.",
                    "📍 Filhaal koi active delivery nahi hai jo track ki ja sake.",
                    "📍 ابھی کوئی فعال ترسیل نہیں جسے ٹریک کیا جا سکے۔"
                );
            }
            $b = $ctx['active_bookings'][0];
            $statusMap = [
                'order_confirmed'    => $t('✅ Order Confirmed',    '✅ Order Confirm Ho Gaya',     '✅ آرڈر کنفرم'),
                'vehicle_dispatched' => $t('🚛 Vehicle Dispatched', '🚛 Vehicle Rawan Ho Gaya',     '🚛 گاڑی روانہ'),
                'in_transit'         => $t('🛣️ In Transit',         '🛣️ Raste Mein Hai',            '🛣️ راستے میں ہے'),
                'delivered'          => $t('📦 Delivered',           '📦 Pahunch Gaya',              '📦 پہنچ گیا'),
            ];
            $statusLabel = $statusMap[$b['delivery_status']] ?? $b['delivery_status'];
            $en = "📍 **Active Delivery — Booking #{$b['id']}:**\n\n📌 {$b['from']} → {$b['to']}\n🚛 {$b['vehicle_number']} ({$b['vehicle_type']})\n📦 {$b['goods_type']} | {$b['goods_weight']}kg\nStatus: **{$statusLabel}** | ETA: {$b['est_duration']}\n\nCheck dashboard for live tracking.";
            $ru = "📍 **Active Delivery — Booking #{$b['id']}:**\n\n📌 {$b['from']} → {$b['to']}\n🚛 {$b['vehicle_number']} ({$b['vehicle_type']})\n📦 {$b['goods_type']} | {$b['goods_weight']}kg\nStatus: **{$statusLabel}** | ETA: {$b['est_duration']}\n\nDashboard mein live tracking available hai.";
            $us = "📍 **فعال ترسیل — بکنگ #{$b['id']}:**\n\n📌 {$b['from']} → {$b['to']}\n🚛 {$b['vehicle_number']} ({$b['vehicle_type']})\n📦 {$b['goods_type']} | {$b['goods_weight']}kg\nحالت: **{$statusLabel}** | ETA: {$b['est_duration']}\n\nڈیش بورڈ میں لائیو ٹریکنگ دیکھیں۔";
            return $t($en, $ru, $us);
        }

        // ── Fare ─────────────────────────────────────────────
        if ($has(['fare','cost','price','kiraya','kitna paise','how much','rate','charge','calculate','estimate','paisa','rupees','rs'])) {
            return $this->extractFareQuery($m, $lang);
        }

        // ── How to Book ──────────────────────────────────────
        if ($has(['how to book','booking kaise','book karna','kaise book','vehicle book','steps to book','booking process','book krna','booking karen','booking ka tarika'])) {
            $en = "📝 **How to Book:**\n\n1️⃣ Go to **Find Vehicles**\n2️⃣ Choose a suitable vehicle\n3️⃣ Click **Book Now**\n4️⃣ Enter pickup & dropoff\n5️⃣ Add goods details\n6️⃣ Submit — confirmed in 5-10 min!";
            $ru = "📝 **Vehicle Booking ka Tarika:**\n\n1️⃣ **Find Vehicles** mein jayen\n2️⃣ Apni zaroorat ka vehicle chunen\n3️⃣ **Book Now** click karein\n4️⃣ Pickup aur dropoff enter karein\n5️⃣ Goods ki details likhein\n6️⃣ Submit karein — 5-10 min mein confirm!";
            $us = "📝 **بکنگ کا طریقہ:**\n\n1️⃣ **Find Vehicles** پر جائیں\n2️⃣ مناسب گاڑی منتخب کریں\n3️⃣ **Book Now** پر کلک کریں\n4️⃣ لوکیشن درج کریں\n5️⃣ سامان کی تفصیل لکھیں\n6️⃣ Submit کریں — 5-10 منٹ میں تصدیق!";
            return $t($en, $ru, $us);
        }

        // ── Complaints ───────────────────────────────────────
        if ($has(['complaint','complain','masla','issue','problem','shikayat','شکایت','complain karo','complain hai','masla hai'])) {
            if ($ctx['total_complaints'] === 0) {
                return $t(
                    "📋 No complaints filed yet. Head to the **Complaints** section if needed.",
                    "📋 Aapne koi complaint file nahi ki. Koi masla ho toh **Complaints** section mein jayen.",
                    "📋 ابھی تک کوئی شکایت نہیں۔ **Complaints** سیکشن میں جائیں۔"
                );
            }
            $header = $t(
                "📋 **Your Complaints:** Total:{$ctx['total_complaints']} | ⏳Pending:{$ctx['pending_complaints']} | ✅Resolved:{$ctx['resolved_complaints']}",
                "📋 **Aapki Complaints:** Total:{$ctx['total_complaints']} | ⏳Pending:{$ctx['pending_complaints']} | ✅Resolved:{$ctx['resolved_complaints']}",
                "📋 **آپ کی شکایات:** کل:{$ctx['total_complaints']} | ⏳زیر التوا:{$ctx['pending_complaints']} | ✅حل شدہ:{$ctx['resolved_complaints']}"
            );
            foreach ($ctx['recent_complaints'] as $c) {
                $icon = $c['status'] === 'resolved' ? '✅' : '⏳';
                $header .= "\n{$icon} **#{$c['id']}: {$c['subject']}** — {$c['status']} | {$c['date']}";
                if ($c['admin_response']) $header .= "\n  💬 " . $t("Admin","Admin","ایڈمن") . ": {$c['admin_response']}";
            }
            return $header;
        }

        // ── Payment ──────────────────────────────────────────
        if ($has(['payment','paid','unpaid','pay','invoice','bill','jazzcash','easypaisa','cash','paisa dena','payment karna'])) {
            $unpaid  = collect($ctx['recent_bookings'])->where('payment_status', 'pending')->count();
            $pendAmt = collect($ctx['recent_bookings'])->where('payment_status', 'pending')->sum('actual_fare');
            $en = "💳 **Payment Info:**\n\n{$unpaid} pending payment(s) — Rs " . number_format($pendAmt) . "\n\n**Methods:** JazzCash • EasyPaisa • Card • Cash on Delivery\n\nVisit **Payments** section.";
            $ru = "💳 **Payment Info:**\n\n{$unpaid} pending payment(s) — Rs " . number_format($pendAmt) . "\n\n**Tarike:** JazzCash • EasyPaisa • Card • Cash on Delivery\n\n**Payments** section mein jayen.";
            $us = "💳 **ادائیگی کی معلومات:**\n\n{$unpaid} زیر التوا ادائیگی — Rs " . number_format($pendAmt) . "\n\n**طریقے:** JazzCash • EasyPaisa • کارڈ • کیش\n\n**Payments** سیکشن میں جائیں۔";
            return $t($en, $ru, $us);
        }

        // ── Reviews ──────────────────────────────────────────
        if ($has(['rating','review','rate','feedback','stars','review karna','review do','review dena'])) {
            $pending = max(0, $ctx['completed_count'] - $ctx['reviews_given']);
            $en = "⭐ You've given **{$ctx['reviews_given']}** reviews out of **{$ctx['completed_count']}** completed bookings." . ($pending > 0 ? "\n\n{$pending} review(s) pending — visit **Ratings & Reviews**!" : "\n\nThank you for the feedback! 🙏");
            $ru = "⭐ Aapne **{$ctx['reviews_given']}** reviews diye hain **{$ctx['completed_count']}** mein se." . ($pending > 0 ? "\n\n{$pending} reviews abhi baki hain — **Ratings & Reviews** mein jayen!" : "\n\nShukriya feedback ke liye! 🙏");
            $us = "⭐ آپ نے **{$ctx['completed_count']}** میں سے **{$ctx['reviews_given']}** ریویو دیے۔" . ($pending > 0 ? "\n\n{$pending} ریویو باقی ہیں — **Ratings & Reviews** میں جائیں!" : "\n\nشکریہ! 🙏");
            return $t($en, $ru, $us);
        }

        // ── Heavy Load ───────────────────────────────────────
        if ($has(['heavy','bhari','ton','industrial','large load','capacity','wajan','heavy truck','bara truck','bhari cheez'])) {
            $heavy = collect($ctx['vehicle_list'])->filter(fn($v) => $v['capacity_kg'] >= 3000 && $v['is_active'] && !$v['is_booked'])->values();
            if ($heavy->isEmpty()) {
                return $t(
                    "🚛 No heavy-capacity vehicles available right now.",
                    "🚛 Filhaal koi heavy vehicle dastiyab nahi hai.",
                    "🚛 ابھی کوئی بھاری گاڑی دستیاب نہیں۔"
                );
            }
            $lines = $heavy->map(fn($v) =>
                "• **{$v['number']}** — {$v['type']} | **{$v['capacity_kg']}kg** | {$v['can_carry']}" .
                ($v['avg_rating'] ? " | ⭐{$v['avg_rating']}" : "") . " | {$v['provider']}"
            )->toArray();
            $header = $t("🚛 **Heavy Load Vehicles (3000kg+):**", "🚛 **Heavy Load Vehicles (3000kg+):**", "🚛 **بھاری گاڑیاں (3000kg+):**");
            return $header . "\n\n" . implode("\n", $lines);
        }

        // ── Penalty ──────────────────────────────────────────
        if ($has(['penalty','late','delay','jukrmana','late delivery','late hona','deri'])) {
            $penaltyBookings = collect($ctx['recent_bookings'])->where('penalty_amount', '>', 0);
            $penLines = $penaltyBookings->map(fn($b) => "• Booking #{$b['id']}: Rs{$b['penalty_amount']}")->implode("\n") ?: $t("None ✅", "Koi nahi ✅", "کوئی نہیں ✅");
            $en = "⏰ **Late Delivery Penalty:**\n\nIf delivery exceeds estimated time, Rs **200/hour** is deducted from provider.\n\nYour penalties:\n{$penLines}";
            $ru = "⏰ **Late Delivery Penalty:**\n\nDelivery late hone par provider se Rs **200/ghanta** kata jata hai.\n\nAapki penalties:\n{$penLines}";
            $us = "⏰ **تاخیر کا جرمانہ:**\n\nدیر سے ڈیلیوری پر فراہم کار سے Rs **200/گھنٹہ** کٹتا ہے۔\n\nآپ کے جرمانے:\n{$penLines}";
            return $t($en, $ru, $us);
        }

        // ── Provider Contact ─────────────────────────────────
        if ($has(['contact','driver','provider','phone number','number kya','call karna','rabta','driver ka number','contact karo'])) {
            if ($ctx['active_count'] === 0) {
                return $t(
                    "📞 No active bookings. Provider contact is available once you book.",
                    "📞 Koi active booking nahi hai. Book karne ke baad provider ka number milega.",
                    "📞 کوئی فعال بکنگ نہیں۔ بکنگ کے بعد فراہم کار کا نمبر ملے گا۔"
                );
            }
            $b = $ctx['active_bookings'][0];
            $vehicle = collect($ctx['vehicle_list'])->firstWhere('number', $b['vehicle_number']);
            $prov = $vehicle['provider']         ?? 'N/A';
            $cont = $vehicle['provider_contact'] ?? 'N/A';
            $en = "📞 **Booking #{$b['id']} Provider:**\n\n🚛 Vehicle: {$b['vehicle_number']}\n👤 {$prov}\n📱 {$cont}";
            $ru = "📞 **Booking #{$b['id']} ka Provider:**\n\n🚛 Vehicle: {$b['vehicle_number']}\n👤 {$prov}\n📱 Contact: {$cont}";
            $us = "📞 **بکنگ #{$b['id']} فراہم کار:**\n\n🚛 گاڑی: {$b['vehicle_number']}\n👤 {$prov}\n📱 {$cont}";
            return $t($en, $ru, $us);
        }

        // ── Default ──────────────────────────────────────────
        $avail = $ctx['available_vehicles'];
        $en = "🤖 I'm **TruckLink AI** — your logistics assistant!\n\n• 🚛 Vehicles ({$avail} available)\n• ⭐ Top rated\n• 📝 How to book\n• 💰 Fare calculator\n• 📦 Active bookings ({$ctx['active_count']})\n• 📍 Track delivery\n• 📋 Complaints\n• 💳 Payments\n\nTry: *\"Available vehicles\"* or *\"Fare from Lahore to Karachi\"*";
        $ru = "🤖 Main **TruckLink AI** hoon — aapka logistics assistant!\n\n• 🚛 Vehicles ({$avail} dastiyab)\n• ⭐ Top rated\n• 📝 Booking ka tarika\n• 💰 Kiraya calculate\n• 📦 Active bookings ({$ctx['active_count']})\n• 📍 Delivery track\n• 📋 Complaints\n• 💳 Payment\n\nTry karein: *\"Dastiyab vehicles\"* ya *\"Lahore se Karachi kiraya\"*";
        $us = "🤖 میں **TruckLink AI** ہوں!\n\n• 🚛 گاڑیاں ({$avail} دستیاب)\n• ⭐ بہترین ریٹڈ\n• 📝 بکنگ کا طریقہ\n• 💰 کرایہ حساب\n• 📦 فعال بکنگ ({$ctx['active_count']})\n• 📍 ٹریکنگ\n• 📋 شکایات\n• 💳 ادائیگی\n\nلکھیں: *\"دستیاب گاڑیاں\"* یا *\"لاہور سے کراچی کرایہ\"*";
        return $t($en, $ru, $us);
    }

    // ══════════════════════════════════════════════════════════
    // FARE EXTRACTION — Trilingual
    // ══════════════════════════════════════════════════════════

    private function extractFareQuery(string $m, string $lang): string
    {
        $distances = [
            'lahore-islamabad'    => 375,  'islamabad-lahore'    => 375,
            'lahore-karachi'      => 1210, 'karachi-lahore'      => 1210,
            'islamabad-karachi'   => 1410, 'karachi-islamabad'   => 1410,
            'lahore-peshawar'     => 490,  'peshawar-lahore'     => 490,
            'lahore-multan'       => 340,  'multan-lahore'       => 340,
            'islamabad-peshawar'  => 170,  'peshawar-islamabad'  => 170,
            'karachi-hyderabad'   => 165,  'hyderabad-karachi'   => 165,
            'lahore-faisalabad'   => 130,  'faisalabad-lahore'   => 130,
            'islamabad-multan'    => 490,  'multan-islamabad'    => 490,
            'lahore-gujranwala'   => 75,   'gujranwala-lahore'   => 75,
            'lahore-sialkot'      => 115,  'sialkot-lahore'      => 115,
            'peshawar-quetta'     => 900,  'quetta-peshawar'     => 900,
            'karachi-quetta'      => 690,  'quetta-karachi'      => 690,
            'rawalpindi-lahore'   => 360,  'lahore-rawalpindi'   => 360,
            'rawalpindi-peshawar' => 175,  'peshawar-rawalpindi' => 175,
        ];

        $cities = ['lahore','islamabad','karachi','peshawar','multan','faisalabad','quetta','hyderabad','gujranwala','sialkot','rawalpindi'];
        $found  = [];
        foreach ($cities as $city) {
            if (str_contains($m, $city)) $found[] = $city;
        }

        if (count($found) >= 2) {
            $dist = $distances[$found[0] . '-' . $found[1]] ?? null;
            if ($dist) {
                $fare = 100 + ($dist * 25);
                $f0   = ucfirst($found[0]);
                $f1   = ucfirst($found[1]);
                if ($lang === self::LANG_URDU) {
                    return "💰 **{$f0} → {$f1} کرایہ:**\n\nفاصلہ: ~{$dist} km\nبیس کرایہ: Rs 100\nفی کلومیٹر: Rs " . ($dist*25) . "\n**کل تخمینہ: ~Rs " . number_format($fare) . "** + ٹول";
                } elseif ($lang === self::LANG_ROMAN_URDU) {
                    return "💰 **{$f0} → {$f1} Kiraya:**\n\nDuuri: ~{$dist} km\nBase: Rs 100\nKilometer: Rs " . ($dist*25) . " (Rs 25 × {$dist})\n**Total Estimate: ~Rs " . number_format($fare) . "** + tolls\n\n_Waiting charges alag se (Rs 50/30min)_";
                } else {
                    return "💰 **{$f0} → {$f1} Fare:**\n\nDistance: ~{$dist} km\nBase: Rs 100 + Rs " . ($dist*25) . " (Rs 25/km)\n**Estimate: ~Rs " . number_format($fare) . "** + tolls\n\n_Waiting: Rs 50/30min_";
                }
            }
        }

        if ($lang === self::LANG_URDU) {
            return "💰 **کرایہ فارمولہ:**\n\n• بیس: Rs 100\n• فاصلہ: Rs 25/km\n• انتظار: Rs 50/30 منٹ\n• ٹول الگ\n\nمثال: 50km = Rs 1,350\n\nشہروں کے نام لکھیں۔";
        } elseif ($lang === self::LANG_ROMAN_URDU) {
            return "💰 **TruckLink Kiraya Formula:**\n\n• Base: Rs 100\n• Duuri: Rs 25/km\n• Wait: Rs 50/30min\n• Toll alag\n\nMisal: 50km = Rs 1,350\n\nCity names likhein exact estimate ke liye — jaise *\"Lahore se Karachi\"*";
        } else {
            return "💰 **Fare Formula:**\n\n• Base: Rs 100 • Distance: Rs 25/km • Waiting: Rs 50/30min • Tolls extra\n\n**50km ≈ Rs 1,350 + tolls**\n\nShare origin & destination for exact estimate!";
        }
    }

    // ══════════════════════════════════════════════════════════
    // SUGGESTIONS — Trilingual
    // ══════════════════════════════════════════════════════════

    private function getSuggestions(string $message, array $ctx, string $lang): array
    {
        $m = strtolower($message);

        $sets = [
            'en' => [
                'vehicle'   => ['Top rated vehicles', 'Available trucks now', 'How to book?'],
                'booking'   => ['Track my delivery', 'Booking history', 'Contact provider'],
                'fare'      => ['Book a vehicle', 'Payment methods', 'Available vehicles'],
                'complaint' => ['Complaint status', 'File new complaint', 'My bookings'],
                'track'     => ['Active booking details', 'Contact driver', 'Fare estimate'],
                'payment'   => ['My bookings', 'Pending payments', 'Payment methods'],
                'default'   => ['Available vehicles', 'My active bookings', 'Calculate fare'],
            ],
            'ru' => [
                'vehicle'   => ['Top rated vehicles', 'Dastiyab vehicles', 'Kaise book karein?'],
                'booking'   => ['Delivery track karein', 'Booking history', 'Provider se rabta'],
                'fare'      => ['Vehicle book karein', 'Payment methods', 'Dastiyab vehicles'],
                'complaint' => ['Complaint status', 'Nai complaint', 'Meri bookings'],
                'track'     => ['Active booking detail', 'Driver se contact', 'Kiraya estimate'],
                'payment'   => ['Meri bookings', 'Pending payments', 'Payment tarike'],
                'default'   => ['Dastiyab vehicles', 'Active bookings', 'Kiraya calculate'],
            ],
            'us' => [
                'vehicle'   => ['بہترین گاڑیاں', 'دستیاب ٹرک', 'بکنگ کا طریقہ؟'],
                'booking'   => ['ڈیلیوری ٹریک', 'بکنگ تاریخ', 'فراہم کار سے رابطہ'],
                'fare'      => ['گاڑی بک کریں', 'ادائیگی طریقے', 'دستیاب گاڑیاں'],
                'complaint' => ['شکایت حالت', 'نئی شکایت', 'میری بکنگ'],
                'track'     => ['فعال بکنگ تفصیل', 'ڈرائیور سے رابطہ', 'کرایہ تخمینہ'],
                'payment'   => ['میری بکنگ', 'زیر التوا ادائیگی', 'طریقے'],
                'default'   => ['دستیاب گاڑیاں', 'فعال بکنگ', 'کرایہ حساب'],
            ],
        ];

        $key = ($lang === self::LANG_URDU) ? 'us' : (($lang === self::LANG_ROMAN_URDU) ? 'ru' : 'en');
        $s   = $sets[$key];

        if (str_contains($m, 'vehicle') || str_contains($m, 'truck') || str_contains($m, 'gaadi') || str_contains($m, 'گاڑی'))
            return $s['vehicle'];
        if (str_contains($m, 'book') || str_contains($m, 'booking') || str_contains($m, 'بکنگ'))
            return $s['booking'];
        if (str_contains($m, 'fare') || str_contains($m, 'kiraya') || str_contains($m, 'cost') || str_contains($m, 'کرایہ'))
            return $s['fare'];
        if (str_contains($m, 'complaint') || str_contains($m, 'masla') || str_contains($m, 'شکایت'))
            return $s['complaint'];
        if (str_contains($m, 'track') || str_contains($m, 'kahan') || str_contains($m, 'ٹریک'))
            return $s['track'];
        if (str_contains($m, 'payment') || str_contains($m, 'pay') || str_contains($m, 'paisa') || str_contains($m, 'ادائیگی'))
            return $s['payment'];

        return $s['default'];
    }

    // ══════════════════════════════════════════════════════════
    // HELPERS
    // ══════════════════════════════════════════════════════════

    private function getTimeGreeting(string $lang): string
    {
        $hour = (int) now()->timezone('Asia/Karachi')->format('H');

        if ($lang === self::LANG_URDU) {
            if ($hour < 12) return "صبح بخیر";
            if ($hour < 17) return "دوپہر بخیر";
            if ($hour < 20) return "شام بخیر";
            return "رات بخیر";
        }

        if ($lang === self::LANG_ROMAN_URDU) {
            if ($hour < 12) return "Subah Bakhair";
            if ($hour < 17) return "Dopahar Bakhair";
            if ($hour < 20) return "Shaam Bakhair";
            return "Salam";
        }

        if ($hour < 12) return "Good Morning";
        if ($hour < 17) return "Good Afternoon";
        if ($hour < 20) return "Good Evening";
        return "Good Evening";
    }

    private function saveChat(int $customerId, string $sender, string $message): void
    {
        try {
            DB::table('chat_histories')->insert([
                'customer_id' => $customerId,
                'sender'      => $sender,
                'message'     => $message,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        } catch (\Exception $e) {
            Log::warning('Chat save failed: ' . $e->getMessage());
        }
    }
}