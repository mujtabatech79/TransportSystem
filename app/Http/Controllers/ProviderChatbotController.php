<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GeminiService;
use App\Models\Vehicle;
use App\Models\Booking;
use App\Models\Complaint;
use App\Models\Review;
use App\Models\Userr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProviderChatbotController extends Controller
{
    protected $gemini;

    // ── Language Modes ────────────────────────────────────────
    const LANG_ENGLISH    = 'english';
    const LANG_URDU       = 'urdu';
    const LANG_ROMAN_URDU = 'roman_urdu';

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
        $providerId = session('user_id');
        $role       = session('role');

        if (!$providerId || $role !== 'provider') {
            return response()->json([
                'success'  => false,
                'response' => 'Session expired. Please log in again to use the assistant.',
            ]);
        }

        if (strlen($message) < 2) {
            return response()->json([
                'success'  => true,
                'response' => 'Please provide more detail — I am here to help! 😊',
            ]);
        }

        $lang    = $this->detectLanguage($message);
        $context = $this->getContext($providerId);
        $history = $this->getRecentHistory($providerId, 6);

        $systemPrompt = $this->buildSystemPrompt($context, $lang);
        $userPrompt   = $this->buildUserPrompt($message, $context, $lang, $history);

        $aiResponse = null;

        try {
            $aiResponse = $this->gemini->generate($userPrompt, $systemPrompt);
        } catch (\Exception $e) {
            Log::error('Provider AI exception: ' . $e->getMessage());
        }

        if (!$aiResponse || strlen(trim($aiResponse)) < 5) {
            $aiResponse = $this->smartFallback($message, $context, $lang);
        }

        $this->saveChat($providerId, 'user', $message);
        $this->saveChat($providerId, 'bot',  $aiResponse);

        return response()->json([
            'success'     => true,
            'response'    => $aiResponse,
            'suggestions' => $this->getSuggestions($message, $context, $lang),
            'lang'        => $lang,
        ]);
    }

    // ══════════════════════════════════════════════════════════
    // LANGUAGE DETECTION
    // ══════════════════════════════════════════════════════════

    private function detectLanguage(string $message): string
    {
        if (preg_match('/[\x{0600}-\x{06FF}]/u', $message)) {
            return self::LANG_URDU;
        }

        $lower = mb_strtolower(trim($message));

        $strongRomanPhrases = [
            'kya hai', 'kya hain', 'kya ho', 'kya kr', 'kya kar',
            'kaise hai', 'kaisi hai', 'kaise hain', 'kesy hai',
            'mujhe batao', 'mujhe btao', 'mujhe chahiye', 'mujhe bta',
            'meri booking', 'mera booking', 'meri vehicle', 'mera vehicle',
            'koi booking', 'kiraya', 'kitna hai', 'kitne hain',
            'truck wala', 'gaadi', 'kahan hai', 'kab tak', 'kab hoga',
            'booking kaise', 'kaise book', 'book karna', 'book krna',
            'nahi hai', 'nhi hai', 'paise', 'masla hai', 'masla kya',
            'shikayat', 'complaint hai', 'help chahiye', 'samjhao',
            'dikhao', 'batao', 'btao', 'bata do', 'bta do',
            'theek hai', 'shukriya', 'bahut acha', 'zyada',
            'customer ne', 'mujhe accept', 'booking accept', 'reject krna',
            'earnings kya', 'kitni kamai', 'vehicle add', 'vehicle dalna',
            'pending request', 'request aayi', 'nayi booking',
        ];

        foreach ($strongRomanPhrases as $phrase) {
            if (str_contains($lower, $phrase)) {
                return self::LANG_ROMAN_URDU;
            }
        }

        $romanKeywords = [
            'hai'  => 2, 'hain' => 2, 'ho'   => 1, 'tha'  => 2, 'thi'  => 2,
            'hoga' => 2, 'hogi' => 2, 'hote' => 2, 'hoti' => 2,
            'main' => 2, 'mein' => 1, 'mera' => 2, 'meri' => 2, 'mujhe'=> 2,
            'ap'   => 1, 'aap'  => 2, 'uska' => 2, 'uski' => 2, 'unka' => 2,
            'yeh'  => 2, 'woh'  => 2,
            'kya'  => 3, 'kaise'=> 3, 'kesy' => 3, 'kyun' => 3,
            'kab'  => 3, 'kahan'=> 3, 'kitna'=> 3, 'kitni'=> 3, 'kitne'=> 3,
            'karo' => 2, 'kren' => 2, 'krna' => 2, 'karna'=> 2,
            'btao' => 3, 'batao'=> 3, 'dikhao'=>3, 'samjhao'=>3,
            'chahiye'=>3, 'aur'  => 1, 'bhi'  => 1, 'toh'  => 1, 'lekin'=> 2,
            'booking'  => 1, 'kiraya'   => 3, 'gaadi'    => 3, 'masla'    => 3,
            'shikayat' => 3, 'paise'    => 3, 'abhi'     => 2, 'theek'    => 2,
            'nahi'     => 2, 'nhi'      => 2, 'zaroor'   => 2,
            'customer' => 1, 'accept'   => 1, 'reject'   => 1, 'earning'  => 1,
        ];

        $words   = preg_split('/\s+/', $lower);
        $score   = 0;
        $matched = 0;

        foreach ($words as $word) {
            $word = preg_replace('/[^a-z]/', '', $word);
            if (isset($romanKeywords[$word])) {
                $score  += $romanKeywords[$word];
                $matched++;
            }
        }

        if ($score >= 4 || $matched >= 2) {
            return self::LANG_ROMAN_URDU;
        }

        return self::LANG_ENGLISH;
    }

    private function isUrduFamily(string $lang): bool
    {
        return in_array($lang, [self::LANG_URDU, self::LANG_ROMAN_URDU]);
    }

    // ══════════════════════════════════════════════════════════
    // CONTEXT BUILDER
    // ══════════════════════════════════════════════════════════

    private function getContext(int $providerId): array
    {
        return Cache::remember("provider_chatbot_context_{$providerId}", now()->addMinutes(3), function () use ($providerId) {
            return $this->buildContext($providerId);
        });
    }

    private function buildContext(int $providerId): array
    {
        $provider   = Userr::find($providerId);
        $myVehicles = Vehicle::where('user_id', $providerId)->get();

        $vehicleList = $myVehicles->map(function ($v) {
            $activeBooking = null;
            if ($v->is_booked === 'yes') {
                $activeBooking = Booking::where('vehicle_id', $v->id)
                    ->where('status', 'accept')
                    ->with('customer')
                    ->latest()
                    ->first();
            }
            $avgRating    = Review::whereHas('booking', fn($q) => $q->where('vehicle_id', $v->id))->avg('rating');
            $totalReviews = Review::whereHas('booking', fn($q) => $q->where('vehicle_id', $v->id))->count();

            return [
                'id'               => $v->id,
                'type'             => $v->vehicle_type    ?? 'Unknown',
                'number'           => $v->vehicle_number  ?? 'N/A',
                'capacity_kg'      => $v->weight_capacity ?? 0,
                'can_carry'        => $v->can_carry        ?? 'General goods',
                'is_active'        => (bool) $v->is_active,
                'is_booked'        => $v->is_booked === 'yes',
                'delivery_status'  => $activeBooking?->delivery_status ?? null,
                'est_duration'     => $activeBooking
                    ? ($activeBooking->duration_text ?? ($activeBooking->estimated_duration ? $activeBooking->estimated_duration . ' min' : null))
                    : null,
                'current_customer' => $activeBooking?->customer?->name ?? null,
                'avg_rating'       => $avgRating ? round($avgRating, 1) : null,
                'total_reviews'    => $totalReviews,
            ];
        })->values()->toArray();

        $vehicleIds  = $myVehicles->pluck('id');
        $allBookings = Booking::whereIn('vehicle_id', $vehicleIds)
            ->with(['customer', 'vehicle'])
            ->get();

        $pendingRequests = $allBookings->where('status', 'request')->values();
        $pendingDetails  = $pendingRequests->map(fn($b) => [
            'id'             => $b->id,
            'customer'       => $b->customer?->name ?? 'N/A',
            'from'           => $b->pickup_location,
            'to'             => $b->dropoff_location,
            'goods_type'     => $b->goods_type    ?? 'N/A',
            'goods_weight'   => $b->goods_weight   ?? 0,
            'est_fare'       => $b->estimated_fare ?? 0,
            'date'           => $b->booking_date ? $b->booking_date->format('d M Y') : 'N/A',
            'vehicle_number' => $b->vehicle?->vehicle_number ?? 'N/A',
        ])->values()->toArray();

        $activeBookings = $allBookings->where('status', 'accept')->values();
        $activeDetails  = $activeBookings->map(fn($b) => [
            'id'              => $b->id,
            'customer'        => $b->customer?->name ?? 'N/A',
            'from'            => $b->pickup_location,
            'to'              => $b->dropoff_location,
            'delivery_status' => $b->delivery_status ?? 'order_confirmed',
            'vehicle_number'  => $b->vehicle?->vehicle_number ?? 'N/A',
            'vehicle_type'    => $b->vehicle?->vehicle_type   ?? 'N/A',
            'est_duration'    => $b->duration_text ?? ($b->estimated_duration ? $b->estimated_duration . ' min' : 'N/A'),
            'est_fare'        => $b->estimated_fare ?? 0,
            'goods_type'      => $b->goods_type    ?? 'N/A',
            'goods_weight'    => $b->goods_weight   ?? 0,
            'accepted_at'     => $b->accepted_at?->format('d M Y H:i') ?? 'N/A',
        ])->values()->toArray();

        $recentBookings = $allBookings->sortByDesc('created_at')->take(10)->map(fn($b) => [
            'id'               => $b->id,
            'customer'         => $b->customer?->name ?? 'N/A',
            'from'             => $b->pickup_location,
            'to'               => $b->dropoff_location,
            'status'           => $b->status,
            'status_text'      => $b->status_text ?? $b->status,
            'goods_type'       => $b->goods_type   ?? 'N/A',
            'goods_weight'     => $b->goods_weight ?? 0,
            'est_fare'         => $b->estimated_fare ?? 0,
            'actual_fare'      => $b->actual_fare    ?? 0,
            'payment_status'   => $b->payment_status ?? 'pending',
            'penalty_amount'   => $b->penalty_amount ?? 0,
            'date'             => $b->booking_date ? $b->booking_date->format('d M Y') : 'N/A',
            'vehicle_number'   => $b->vehicle?->vehicle_number ?? 'N/A',
            'rejection_reason' => $b->rejection_reason ?? null,
        ])->values()->toArray();

        $completedBookings = $allBookings->where('status', 'complete');
        $totalEarnings     = (float) $completedBookings->sum('actual_fare');
        $thisMonthEarnings = (float) $allBookings->where('status', 'complete')
            ->filter(fn($b) => $b->delivered_at && \Carbon\Carbon::parse($b->delivered_at)->isCurrentMonth())
            ->sum('actual_fare');
        $totalPenalties = (float) $completedBookings->sum('penalty_amount');

        $complaints       = Complaint::where('provider_id', $providerId)->get();
        $recentComplaints = $complaints->sortByDesc('created_at')->take(5)->map(fn($c) => [
            'id'             => $c->id,
            'subject'        => $c->subject        ?? 'N/A',
            'type'           => $c->complaint_type ?? 'N/A',
            'status'         => $c->status,
            'admin_response' => $c->admin_response ?? null,
            'date'           => $c->created_at?->format('d M Y') ?? 'N/A',
        ])->values()->toArray();

        $allReviews        = Review::where('provider_id', $providerId)->get();
        $avgProviderRating = $allReviews->avg('rating');

        return [
            'provider_id'         => $providerId,
            'provider_name'       => $provider?->name ?? session('name', 'Provider'),
            'provider_email'      => $provider?->email ?? 'N/A',
            'provider_mobile'     => $provider?->mobile ?? 'N/A',
            'total_vehicles'      => $myVehicles->count(),
            'available_vehicles'  => $myVehicles->where('is_active', true)->where('is_booked', 'no')->count(),
            'booked_vehicles'     => $myVehicles->where('is_booked', 'yes')->count(),
            'inactive_vehicles'   => $myVehicles->where('is_active', false)->count(),
            'vehicle_list'        => $vehicleList,
            'total_bookings'      => $allBookings->count(),
            'pending_count'       => $pendingRequests->count(),
            'active_count'        => $activeBookings->count(),
            'completed_count'     => $completedBookings->count(),
            'rejected_count'      => $allBookings->where('status', 'reject')->count(),
            'pending_bookings'    => $pendingDetails,
            'active_bookings'     => $activeDetails,
            'recent_bookings'     => $recentBookings,
            'total_earnings'      => $totalEarnings,
            'this_month_earnings' => $thisMonthEarnings,
            'total_penalties'     => $totalPenalties,
            'total_complaints'    => $complaints->count(),
            'pending_complaints'  => $complaints->where('status', 'pending')->count(),
            'resolved_complaints' => $complaints->where('status', 'resolved')->count(),
            'recent_complaints'   => $recentComplaints,
            'total_reviews'       => $allReviews->count(),
            'avg_rating'          => $avgProviderRating ? round($avgProviderRating, 1) : null,
        ];
    }

    // ══════════════════════════════════════════════════════════
    // CHAT HISTORY — Provider-isolated
    // ══════════════════════════════════════════════════════════

    public function getChatHistory()
    {
        $providerId = session('user_id');
        $role       = session('role');

        if (!$providerId || $role !== 'provider') {
            return response()->json(['success' => false, 'history' => []]);
        }

        $history = DB::table('chat_histories')
            ->where('customer_id', $providerId)
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
        $providerId = session('user_id');
        $role       = session('role');

        if ($providerId && $role === 'provider') {
            DB::table('chat_histories')->where('customer_id', $providerId)->delete();
            Cache::forget("provider_chatbot_context_{$providerId}");
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
    // RECENT CHAT HISTORY FOR AI CONTEXT
    // ══════════════════════════════════════════════════════════

    private function getRecentHistory(int $providerId, int $limit = 6): array
    {
        return DB::table('chat_histories')
            ->where('customer_id', $providerId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get(['sender', 'message'])
            ->reverse()->values()->toArray();
    }

    // ══════════════════════════════════════════════════════════
    // SYSTEM PROMPT
    // ══════════════════════════════════════════════════════════

    private function buildSystemPrompt(array $ctx, string $lang): string
    {
        if ($lang === self::LANG_URDU) {
            $langRule = "USER NE URDU SCRIPT MEIN LIKHA — آپ کو لازمی اردو اسکرپٹ میں جواب دینا ہے۔ انگریزی بالکل استعمال نہ کریں جب تک user خود انگریزی نہ لکھے۔";
        } elseif ($lang === self::LANG_ROMAN_URDU) {
            $langRule = "USER NE ROMAN URDU MEIN LIKHA — sirf Roman Urdu mein jawab dein. English bilkul mat use karein.";
        } else {
            $langRule = "USER WROTE IN ENGLISH — respond in clear, professional English only.";
        }

        $p  = "You are TruckLink Provider AI — the intelligent assistant exclusively for SERVICE PROVIDERS on TruckLink Pakistan's logistics platform.\n\n";
        $p .= "## ⚠️ CRITICAL LANGUAGE RULE ⚠️\n";
        $p .= $langRule . "\n";
        $p .= "MIRROR the user's language EXACTLY. This overrides everything.\n\n";
        $p .= "## PERSONALITY\n";
        $p .= "- Professional logistics manager tone\n";
        $p .= "- Warm, knowledgeable, proactive with suggestions\n";
        $p .= "- Max 1-2 emojis. Max 130 words per response.\n";
        $p .= "- ONLY use real data below — never invent.\n\n";
        $p .= "## PROVIDER IDENTITY\n";
        $p .= "Name: {$ctx['provider_name']} | Email: {$ctx['provider_email']} | Mobile: {$ctx['provider_mobile']}\n\n";
        $p .= "## TRUCKLINK PROVIDER PLATFORM\n";
        $p .= "- Fare: Rs 100 base + Rs 25/km + Rs 50/30min wait + tolls\n";
        $p .= "- Late delivery penalty: Rs 200/hour (deducted from your fare)\n";
        $p .= "- Accept/Reject bookings from: Booking Requests section\n";
        $p .= "- Update delivery: Dashboard > Active Bookings > Update Status\n";
        $p .= "- Add vehicle: My Vehicles > Add New Vehicle\n";
        $p .= "- Payment: Received after delivery confirmation\n\n";

        $p .= "## YOUR VEHICLES ({$ctx['total_vehicles']} total)\n";
        $p .= "Available:{$ctx['available_vehicles']} | Booked:{$ctx['booked_vehicles']} | Inactive:{$ctx['inactive_vehicles']}\n";
        foreach ($ctx['vehicle_list'] as $v) {
            $st = $v['is_booked']
                ? "ON TRIP" . ($v['delivery_status'] ? "[{$v['delivery_status']}]" : '') . ($v['current_customer'] ? " Customer:{$v['current_customer']}" : '')
                : ($v['is_active'] ? "AVAILABLE" : "INACTIVE");
            $rt = $v['avg_rating'] ? " ⭐{$v['avg_rating']}({$v['total_reviews']} reviews)" : " (no reviews yet)";
            $p .= "  [{$v['number']}] {$v['type']} | {$v['capacity_kg']}kg | {$v['can_carry']} | {$st}{$rt}\n";
        }

        $p .= "\n## YOUR BOOKINGS\n";
        $p .= "Total:{$ctx['total_bookings']} | ⏳Pending:{$ctx['pending_count']} | 🚛Active:{$ctx['active_count']} | ✅Done:{$ctx['completed_count']} | ❌Rejected:{$ctx['rejected_count']}\n";

        if (!empty($ctx['pending_bookings'])) {
            $p .= "\n⚠️ PENDING REQUESTS (need action):\n";
            foreach ($ctx['pending_bookings'] as $b) {
                $p .= "  Request#{$b['id']}: Customer:{$b['customer']} | {$b['from']}→{$b['to']} | {$b['vehicle_number']} | {$b['goods_type']} {$b['goods_weight']}kg | Rs{$b['est_fare']} | {$b['date']}\n";
            }
        }

        if (!empty($ctx['active_bookings'])) {
            $p .= "\n🚛 ACTIVE TRIPS:\n";
            foreach ($ctx['active_bookings'] as $b) {
                $p .= "  Trip#{$b['id']}: Customer:{$b['customer']} | {$b['from']}→{$b['to']} | {$b['vehicle_number']} | {$b['delivery_status']} | ETA:{$b['est_duration']} | {$b['goods_type']} {$b['goods_weight']}kg | Rs{$b['est_fare']}\n";
            }
        }

        if (!empty($ctx['recent_bookings'])) {
            $p .= "\nRECENT BOOKINGS:\n";
            foreach ($ctx['recent_bookings'] as $b) {
                $pen = $b['penalty_amount'] > 0 ? " Penalty:Rs{$b['penalty_amount']}" : "";
                $rej = $b['rejection_reason'] ? " Reason:{$b['rejection_reason']}" : "";
                $p .= "  Booking#{$b['id']}: {$b['customer']} | {$b['from']}→{$b['to']} | {$b['vehicle_number']} | {$b['status_text']} | Rs{$b['actual_fare']} | {$b['payment_status']} | {$b['date']}{$pen}{$rej}\n";
            }
        }

        $p .= "\n## YOUR EARNINGS\n";
        $p .= "Total Earned: Rs" . number_format($ctx['total_earnings']) . " | This Month: Rs" . number_format($ctx['this_month_earnings']) . " | Total Penalties: Rs" . number_format($ctx['total_penalties']) . "\n";

        $p .= "\n## COMPLAINTS AGAINST YOU\n";
        $p .= "Total:{$ctx['total_complaints']} | Pending:{$ctx['pending_complaints']} | Resolved:{$ctx['resolved_complaints']}\n";
        foreach ($ctx['recent_complaints'] as $c) {
            $resp = $c['admin_response'] ? "AdminReply:{$c['admin_response']}" : "AwaitingReply";
            $p .= "  Complaint#{$c['id']}: {$c['subject']}({$c['type']}) | {$c['status']} | {$resp} | {$c['date']}\n";
        }

        $p .= "\n## YOUR RATINGS\n";
        $p .= "Total Reviews:{$ctx['total_reviews']} | Avg Rating:" . ($ctx['avg_rating'] ? "⭐{$ctx['avg_rating']}/5" : "No ratings yet") . "\n";

        return $p;
    }

    private function buildUserPrompt(string $message, array $ctx, string $lang, array $history): string
    {
        $historyText = '';
        if (!empty($history)) {
            $historyText = "RECENT CONVERSATION:\n";
            foreach ($history as $h) {
                $role = $h->sender === 'user' ? 'Provider' : 'TruckLink AI';
                $historyText .= "{$role}: {$h->message}\n";
            }
            $historyText .= "\n";
        }

        if ($lang === self::LANG_URDU) {
            $langNote = "⚠️ USER NE URDU SCRIPT MEIN LIKHA — صرف اردو میں جواب دیں۔";
        } elseif ($lang === self::LANG_ROMAN_URDU) {
            $langNote = "⚠️ USER NE ROMAN URDU MEIN LIKHA — sirf Roman Urdu mein jawab dein. English bilkul nahi.";
        } else {
            $langNote = "⚠️ USER WROTE IN ENGLISH — reply in English only.";
        }

        return "{$historyText}PROVIDER MESSAGE: \"{$message}\"\n\n{$langNote}\nUse ONLY real data from context. Vehicle numbers not IDs. Max 130 words.";
    }

    // ══════════════════════════════════════════════════════════
    // SMART FALLBACK
    // ══════════════════════════════════════════════════════════

    private function smartFallback(string $message, array $ctx, string $lang): string
    {
        $m   = mb_strtolower($message);
        $has = fn(array $words) => collect($words)->contains(fn($w) => str_contains($m, $w));

        $t = function(string $en, string $ru, string $us) use ($lang): string {
            if ($lang === self::LANG_URDU)       return $us;
            if ($lang === self::LANG_ROMAN_URDU) return $ru;
            return $en;
        };

        if ($has(['hi','hello','hey','salam','assalam','walaikum','good morning','good afternoon','good evening','السلام','وعلیکم','as salam'])) {
            $greet   = $this->getTimeGreeting($lang);
            $pending = $ctx['pending_count'];
            $en = "👋 {$greet}, **{$ctx['provider_name']}**! Welcome to TruckLink Provider Panel.\n\n📊 Quick summary: **{$ctx['total_vehicles']}** vehicles | **{$pending}** pending request(s) | Earnings: **Rs " . number_format($ctx['total_earnings']) . "**\n\n" . ($pending > 0 ? "⚠️ You have {$pending} booking request(s) awaiting your decision!" : "No pending requests right now. All good! ✅");
            $ru = "👋 {$greet}, **{$ctx['provider_name']}** bhai! TruckLink Provider Panel mein khush aamdeed.\n\n📊 Khulasa: **{$ctx['total_vehicles']}** vehicles | **{$pending}** pending request(s) | Kamai: **Rs " . number_format($ctx['total_earnings']) . "**\n\n" . ($pending > 0 ? "⚠️ {$pending} booking request(s) ka jawab dena hai!" : "Koi pending request nahi. Sub theek hai! ✅");
            $us = "👋 {$greet}! **{$ctx['provider_name']}** صاحب، ٹرک لنک پینل میں خوش آمدید۔\n\n📊 خلاصہ: **{$ctx['total_vehicles']}** گاڑیاں | **{$pending}** زیر التوا درخواست | کمائی: **Rs " . number_format($ctx['total_earnings']) . "**\n\n" . ($pending > 0 ? "⚠️ {$pending} درخواست کا جواب دینا ہے!" : "کوئی زیر التوا درخواست نہیں۔ ✅");
            return $t($en, $ru, $us);
        }

        if ($has(['pending request','pending booking','new request','nayi request','booking request','awaiting','request aayi','koi request','pending ha','pending hain','booking requests','new booking','incoming'])) {
            if ($ctx['pending_count'] === 0) {
                return $t(
                    "📋 No pending booking requests right now. When customers book your vehicles, requests will appear in **Booking Requests** section.",
                    "📋 Filhaal koi pending booking request nahi hai. Jab customer aapki vehicle book kare, request **Booking Requests** section mein aayegi.",
                    "📋 ابھی کوئی زیر التوا درخواست نہیں۔ جب کوئی گاڑی بک کرے تو درخواست **Booking Requests** میں آئے گی۔"
                );
            }
            $header = $t(
                "⚠️ **{$ctx['pending_count']} Pending Booking Request(s) — Action Required:**",
                "⚠️ **{$ctx['pending_count']} Pending Booking Request(s) — Jawab Dena Zaroori:**",
                "⚠️ **{$ctx['pending_count']} زیر التوا درخواستیں — جواب ضروری ہے:**"
            );
            $lines = array_map(fn($b) =>
                "• **Request #{$b['id']}** — {$b['date']}\n" .
                "  👤 Customer: {$b['customer']}\n" .
                "  📍 {$b['from']} → {$b['to']}\n" .
                "  🚛 {$b['vehicle_number']} | 📦 {$b['goods_type']} ({$b['goods_weight']}kg)\n" .
                "  💰 Est. Fare: Rs{$b['est_fare']}",
                $ctx['pending_bookings']
            );
            $footer = $t(
                "\n\nGo to **Booking Requests** to Accept or Reject.",
                "\n\n**Booking Requests** mein jayen Accept/Reject karne ke liye.",
                "\n\n**Booking Requests** میں جائیں Accept/Reject کریں۔"
            );
            return $header . "\n\n" . implode("\n\n", $lines) . $footer;
        }

        if ($has(['how to accept','accept kaise','reject kaise','how to reject','kaise accept','booking accept karna','booking reject karna','accept booking','reject booking','booking ka jawab','request accept'])) {
            $en = "✅ **How to Accept/Reject Bookings:**\n\n1️⃣ Go to **Booking Requests** in sidebar\n2️⃣ Review customer details, route & fare\n3️⃣ Click **Accept** ✅ to confirm or **Reject** ❌ with a reason\n4️⃣ Customer is notified immediately\n5️⃣ After accepting, update delivery status as trip progresses";
            $ru = "✅ **Booking Accept/Reject ka Tarika:**\n\n1️⃣ Sidebar mein **Booking Requests** mein jayen\n2️⃣ Customer detail, route aur fare check karein\n3️⃣ **Accept** ✅ ya **Reject** ❌ karein (reject pe reason likhein)\n4️⃣ Customer ko foran notification milegi\n5️⃣ Accept ke baad delivery status update karte rahein";
            $us = "✅ **بکنگ Accept/Reject کا طریقہ:**\n\n1️⃣ **Booking Requests** میں جائیں\n2️⃣ تفصیل چیک کریں\n3️⃣ **Accept** ✅ یا **Reject** ❌ کریں\n4️⃣ گاہک کو فوری اطلاع ملے گی\n5️⃣ Accept کے بعد ڈیلیوری اسٹیٹس اپڈیٹ کرتے رہیں";
            return $t($en, $ru, $us);
        }

        if ($has(['active trip','active booking','current trip','current booking','chal rahi trip','chal rahi booking','active ha','active hain','ongoing trip','delivery chal','abhi ka trip'])) {
            if ($ctx['active_count'] === 0) {
                return $t(
                    "🚛 No active trips right now. Accept a booking request to start a trip!",
                    "🚛 Filhaal koi active trip nahi hai. Booking request accept karein trip shuru karne ke liye!",
                    "🚛 ابھی کوئی فعال سفر نہیں۔ بکنگ درخواست قبول کریں!"
                );
            }
            $lines = array_map(fn($b) =>
                "• **Trip #{$b['id']}** | Accepted: {$b['accepted_at']}\n" .
                "  👤 {$b['customer']}\n" .
                "  📍 {$b['from']} → {$b['to']}\n" .
                "  🚛 {$b['vehicle_number']} ({$b['vehicle_type']})\n" .
                "  📦 {$b['goods_type']} {$b['goods_weight']}kg | 💰 Rs{$b['est_fare']}\n" .
                "  📌 Status: **{$b['delivery_status']}** | ETA: {$b['est_duration']}",
                $ctx['active_bookings']
            );
            $header = $t("🚛 **{$ctx['active_count']} Active Trip(s):**", "🚛 **{$ctx['active_count']} Active Trip(s):**", "🚛 **{$ctx['active_count']} فعال سفر:**");
            return $header . "\n\n" . implode("\n\n", $lines);
        }

        if ($has(['update status','delivery status','status update','status kaise','dispatched','in transit','delivered','rawan karna','dispatch karna','status badlo','status change'])) {
            $en = "📦 **Update Delivery Status:**\n\n1️⃣ Go to **Active Bookings** in dashboard\n2️⃣ Find your active trip\n3️⃣ Click **Update Status**\n4️⃣ Select stage:\n   • ✅ Order Confirmed\n   • 🚛 Vehicle Dispatched\n   • 🛣️ In Transit\n   • 📦 Delivered\n\n⚠️ Late delivery incurs Rs 200/hour penalty!";
            $ru = "📦 **Delivery Status Update ka Tarika:**\n\n1️⃣ **Active Bookings** mein jayen\n2️⃣ Apna active trip dhundein\n3️⃣ **Update Status** click karein\n4️⃣ Stage select karein:\n   • ✅ Order Confirmed\n   • 🚛 Vehicle Dispatched\n   • 🛣️ In Transit\n   • 📦 Delivered\n\n⚠️ Deri par Rs 200/ghanta penalty lagti hai!";
            $us = "📦 **ڈیلیوری اسٹیٹس اپڈیٹ:**\n\n1️⃣ **Active Bookings** میں جائیں\n2️⃣ **Update Status** پر کلک کریں\n3️⃣ مرحلہ منتخب کریں:\n   • ✅ آرڈر کنفرم\n   • 🚛 گاڑی روانہ\n   • 🛣️ راستے میں\n   • 📦 پہنچ گیا\n\n⚠️ تاخیر پر Rs 200/گھنٹہ جرمانہ!";
            return $t($en, $ru, $us);
        }

        if ($has(['my vehicle','meri vehicle','my truck','mera truck','vehicle list','truck list','konsi vehicles','meri gaadi','vehicle dekho','vehicles dikhao','vehicle status','apni vehicles'])) {
            if ($ctx['total_vehicles'] === 0) {
                return $t(
                    "🚛 You haven't added any vehicles yet. Go to **My Vehicles** → **Add New Vehicle** to get started!",
                    "🚛 Aapne abhi tak koi vehicle add nahi ki. **My Vehicles** → **Add New Vehicle** mein jayen!",
                    "🚛 ابھی تک کوئی گاڑی نہیں۔ **My Vehicles** میں جائیں اور گاڑی شامل کریں!"
                );
            }
            $lines = array_map(function($v) use ($t) {
                $status = $v['is_booked']
                    ? $t('🔴 On Trip' . ($v['current_customer'] ? " ({$v['current_customer']})" : ''),
                         '🔴 Trip Pe Hai' . ($v['current_customer'] ? " ({$v['current_customer']})" : ''),
                         '🔴 سفر پر' . ($v['current_customer'] ? " ({$v['current_customer']})" : ''))
                    : ($v['is_active']
                        ? $t('🟢 Available', '🟢 Dastiyab', '🟢 دستیاب')
                        : $t('⚫ Inactive', '⚫ Inactive', '⚫ غیر فعال'));
                $rt = $v['avg_rating'] ? " | ⭐{$v['avg_rating']}({$v['total_reviews']})" : "";
                return "• **{$v['number']}** — {$v['type']} | {$v['capacity_kg']}kg | {$v['can_carry']} | {$status}{$rt}";
            }, $ctx['vehicle_list']);
            $header = $t(
                "🚛 **Your Vehicles ({$ctx['total_vehicles']} total):**\n✅Available: {$ctx['available_vehicles']} | 🔴On Trip: {$ctx['booked_vehicles']} | ⚫Inactive: {$ctx['inactive_vehicles']}",
                "🚛 **Aapki Vehicles ({$ctx['total_vehicles']} total):**\n✅Dastiyab: {$ctx['available_vehicles']} | 🔴Trip Pe: {$ctx['booked_vehicles']} | ⚫Inactive: {$ctx['inactive_vehicles']}",
                "🚛 **آپ کی گاڑیاں ({$ctx['total_vehicles']} کل):**\n✅دستیاب: {$ctx['available_vehicles']} | 🔴سفر: {$ctx['booked_vehicles']} | ⚫غیر فعال: {$ctx['inactive_vehicles']}"
            );
            return $header . "\n\n" . implode("\n", $lines);
        }

        if ($has(['add vehicle','add truck','new vehicle','nayi vehicle','vehicle add','vehicle daalna','vehicle register','gadi add'])) {
            $en = "➕ **How to Add a New Vehicle:**\n\n1️⃣ Go to **My Vehicles** in sidebar\n2️⃣ Click **Add New Vehicle**\n3️⃣ Fill in:\n   • Vehicle Type & Number\n   • Weight Capacity\n   • Goods it can carry\n   • Upload vehicle & smartcard images\n4️⃣ Submit for admin approval\n\nOnce approved, your vehicle will appear in customer searches!";
            $ru = "➕ **Nayi Vehicle Add karne ka Tarika:**\n\n1️⃣ **My Vehicles** mein jayen\n2️⃣ **Add New Vehicle** click karein\n3️⃣ Fill karein:\n   • Vehicle type aur number\n   • Weight capacity\n   • Goods type\n   • Vehicle aur smartcard images\n4️⃣ Admin approval ke liye submit karein\n\nApprove hone ke baad customers book kar sakenge!";
            $us = "➕ **نئی گاڑی شامل کرنے کا طریقہ:**\n\n1️⃣ **My Vehicles** میں جائیں\n2️⃣ **Add New Vehicle** پر کلک کریں\n3️⃣ تفصیل بھریں\n4️⃣ Admin منظوری کے لیے جمع کریں\n\nمنظوری کے بعد گاہک بک کر سکیں گے!";
            return $t($en, $ru, $us);
        }

        if ($has(['earnings','earning','kamai','kitni kamai','income','revenue','paisa mila','payment mili','how much earned','mujhe kitna mila','total earning','is month','is mahine'])) {
            $en = "💰 **Your Earnings Summary:**\n\n• Total Earned: **Rs " . number_format($ctx['total_earnings']) . "**\n• This Month: **Rs " . number_format($ctx['this_month_earnings']) . "**\n• Penalties Deducted: **Rs " . number_format($ctx['total_penalties']) . "**\n• Completed Bookings: **{$ctx['completed_count']}**\n\nEarnings are transferred after delivery confirmation. Visit **Analytics** for detailed breakdown.";
            $ru = "💰 **Aapki Kamai ka Khulasa:**\n\n• Kul Kamai: **Rs " . number_format($ctx['total_earnings']) . "**\n• Is Mahine: **Rs " . number_format($ctx['this_month_earnings']) . "**\n• Penalties Kati: **Rs " . number_format($ctx['total_penalties']) . "**\n• Complete Bookings: **{$ctx['completed_count']}**\n\nDelivery confirm hone ke baad payment milti hai. **Analytics** mein details dekhein.";
            $us = "💰 **آپ کی کمائی کا خلاصہ:**\n\n• کل کمائی: **Rs " . number_format($ctx['total_earnings']) . "**\n• اس مہینے: **Rs " . number_format($ctx['this_month_earnings']) . "**\n• جرمانے کٹے: **Rs " . number_format($ctx['total_penalties']) . "**\n• مکمل بکنگ: **{$ctx['completed_count']}**\n\nڈیلیوری تصدیق کے بعد ادائیگی ملتی ہے۔";
            return $t($en, $ru, $us);
        }

        if ($has(['penalty','late penalty','deri penalty','jukrmana','late delivery','deri hogayi','penalty kaisi','penalty kyu','penalty kya'])) {
            $penaltyBookings = collect($ctx['recent_bookings'])->where('penalty_amount', '>', 0);
            $penLines = $penaltyBookings->map(fn($b) =>
                "• Booking #{$b['id']} ({$b['customer']}): Rs" . number_format($b['penalty_amount'])
            )->implode("\n");

            if ($ctx['total_penalties'] == 0) {
                return $t(
                    "✅ **No penalties yet!** You've been delivering on time.\n\nReminder: Rs 200/hour is deducted if delivery exceeds estimated time.",
                    "✅ **Koi penalty nahi!** Aap time par deliver kar rahe hain.\n\nYaad rahein: Deri par Rs 200/ghanta katega.",
                    "✅ **کوئی جرمانہ نہیں!** آپ وقت پر ڈیلیوری کر رہے ہیں۔\n\nیاد رہے: تاخیر پر Rs 200/گھنٹہ کٹے گا۔"
                );
            }

            $en = "⏰ **Penalty Summary:**\n\nTotal Deducted: Rs " . number_format($ctx['total_penalties']) . "\nRule: Rs 200/hour for late delivery\n\nPenalized Trips:\n{$penLines}\n\nTip: Update delivery status promptly to avoid penalties!";
            $ru = "⏰ **Penalty Summary:**\n\nKul Kati Gai: Rs " . number_format($ctx['total_penalties']) . "\nRule: Rs 200/ghanta late delivery par\n\nPenalty wali trips:\n{$penLines}\n\nTip: Status time par update karein penalty bachne ke liye!";
            $us = "⏰ **جرمانہ خلاصہ:**\n\nکل کٹا: Rs " . number_format($ctx['total_penalties']) . "\nقانون: Rs 200/گھنٹہ تاخیر پر\n\nجرمانے والی ٹرپس:\n{$penLines}";
            return $t($en, $ru, $us);
        }

        if ($has(['complaint','complain','shikayat','masla','issue','problem','شکایت','complaint against me','complaint aayi','mujhpe complaint'])) {
            if ($ctx['total_complaints'] === 0) {
                return $t(
                    "✅ No complaints filed against you. Keep up the great service!",
                    "✅ Aapke khilaaf koi complaint nahi. Acha kaam karte rahein!",
                    "✅ آپ کے خلاف کوئی شکایت نہیں۔ اچھا کام جاری رکھیں!"
                );
            }
            $header = $t(
                "📋 **Complaints Against You:** Total:{$ctx['total_complaints']} | ⏳Pending:{$ctx['pending_complaints']} | ✅Resolved:{$ctx['resolved_complaints']}",
                "📋 **Aapke Khilaaf Complaints:** Total:{$ctx['total_complaints']} | ⏳Pending:{$ctx['pending_complaints']} | ✅Resolved:{$ctx['resolved_complaints']}",
                "📋 **آپ کے خلاف شکایات:** کل:{$ctx['total_complaints']} | ⏳زیر التوا:{$ctx['pending_complaints']} | ✅حل شدہ:{$ctx['resolved_complaints']}"
            );
            foreach ($ctx['recent_complaints'] as $c) {
                $icon = $c['status'] === 'resolved' ? '✅' : '⏳';
                $header .= "\n{$icon} **#{$c['id']}: {$c['subject']}** — {$c['status']} | {$c['date']}";
                if ($c['admin_response']) $header .= "\n  💬 Admin: {$c['admin_response']}";
            }
            return $header;
        }

        if ($has(['rating','review','feedback','stars','meri rating','apni rating','customer review','mujhe kitne stars','review aaye'])) {
            $avgStr = $ctx['avg_rating'] ? "⭐{$ctx['avg_rating']}/5" : $t("No ratings yet", "Abhi koi rating nahi", "ابھی کوئی ریٹنگ نہیں");
            $en = "⭐ **Your Provider Ratings:**\n\n• Overall Rating: **{$avgStr}**\n• Total Reviews: **{$ctx['total_reviews']}**\n• Completed Deliveries: **{$ctx['completed_count']}**\n\nCheck **Ratings & Reviews** section for detailed feedback from customers.";
            $ru = "⭐ **Aapki Provider Rating:**\n\n• Overall: **{$avgStr}**\n• Total Reviews: **{$ctx['total_reviews']}**\n• Complete Deliveries: **{$ctx['completed_count']}**\n\n**Ratings & Reviews** section mein customer feedback dekhein.";
            $us = "⭐ **آپ کی ریٹنگ:**\n\n• مجموعی ریٹنگ: **{$avgStr}**\n• کل ریویو: **{$ctx['total_reviews']}**\n• مکمل ڈیلیوریاں: **{$ctx['completed_count']}**\n\n**Ratings & Reviews** میں تفصیل دیکھیں۔";
            return $t($en, $ru, $us);
        }

        if ($has(['booking history','all booking','total booking','booking summary','booking stats','meri sari booking','sab bookings','kitni bookings','booking dekho'])) {
            $en = "📊 **Booking Summary — {$ctx['provider_name']}:**\n\n• Total: **{$ctx['total_bookings']}**\n• ⏳ Pending: **{$ctx['pending_count']}**\n• 🚛 Active: **{$ctx['active_count']}**\n• ✅ Completed: **{$ctx['completed_count']}**\n• ❌ Rejected: **{$ctx['rejected_count']}**\n• 💰 Total Earned: **Rs " . number_format($ctx['total_earnings']) . "**";
            $ru = "📊 **Booking Summary — {$ctx['provider_name']}:**\n\n• Total: **{$ctx['total_bookings']}**\n• ⏳ Pending: **{$ctx['pending_count']}**\n• 🚛 Active: **{$ctx['active_count']}**\n• ✅ Complete: **{$ctx['completed_count']}**\n• ❌ Reject: **{$ctx['rejected_count']}**\n• 💰 Kamai: **Rs " . number_format($ctx['total_earnings']) . "**";
            $us = "📊 **بکنگ خلاصہ — {$ctx['provider_name']}:**\n\n• کل: **{$ctx['total_bookings']}**\n• ⏳ زیر التوا: **{$ctx['pending_count']}**\n• 🚛 فعال: **{$ctx['active_count']}**\n• ✅ مکمل: **{$ctx['completed_count']}**\n• ❌ مسترد: **{$ctx['rejected_count']}**\n• 💰 کمائی: **Rs " . number_format($ctx['total_earnings']) . "**";
            return $t($en, $ru, $us);
        }

        if ($has(['recent booking','last booking','latest booking','akhri booking','pichli booking','nayi booking','aakhri'])) {
            if (empty($ctx['recent_bookings'])) {
                return $t("📋 No bookings yet.", "📋 Abhi tak koi booking nahi.", "📋 ابھی تک کوئی بکنگ نہیں۔");
            }
            $lines = array_map(fn($b) =>
                "• **Booking #{$b['id']}** | {$b['date']}\n" .
                "  👤 {$b['customer']} | 📍 {$b['from']} → {$b['to']}\n" .
                "  🚛 {$b['vehicle_number']} | **{$b['status_text']}**\n" .
                "  💰 Rs{$b['actual_fare']} | {$b['payment_status']}" .
                ($b['penalty_amount'] > 0 ? " | Penalty: Rs{$b['penalty_amount']}" : "") .
                ($b['rejection_reason'] ? "\n  ❌ " . $t("Reason","Wajah","وجہ") . ": {$b['rejection_reason']}" : ""),
                $ctx['recent_bookings']
            );
            $header = $t("📋 **Recent Bookings:**", "📋 **Recent Bookings:**", "📋 **حالیہ بکنگز:**");
            return $header . "\n\n" . implode("\n\n", $lines);
        }

        if ($has(['fare','kiraya','rate','charge','cost','price','kitna milega','kitna paisa','estimate','kitni kamai hogi'])) {
            return $this->fareInfo($m, $lang);
        }

        if ($has(['tips','advice','improve','better','zyada kamai','ziada earning','kaise zyada','better rating','good provider','achha provider'])) {
            $en = "💡 **Tips to Improve Your Earnings & Rating:**\n\n1️⃣ Accept requests quickly (within 10 min)\n2️⃣ Always deliver on time — avoid Rs 200/hr penalty\n3️⃣ Keep vehicles well-maintained\n4️⃣ Communicate with customers proactively\n5️⃣ Update delivery status in real-time\n6️⃣ Respond to complaints promptly";
            $ru = "💡 **Kamai aur Rating Behtar karne ke Tips:**\n\n1️⃣ Request jaldi accept karein (10 min ke andar)\n2️⃣ Time par deliver karein — Rs 200/ghanta penalty bachayein\n3️⃣ Vehicle maintain rakhein\n4️⃣ Customer se proactive rabta rakhein\n5️⃣ Delivery status real-time update karein\n6️⃣ Complaints ka foran jawab dein";
            $us = "💡 **کمائی اور ریٹنگ بہتر کرنے کے نکات:**\n\n1️⃣ درخواست جلدی قبول کریں\n2️⃣ وقت پر ڈیلیوری کریں\n3️⃣ گاڑی ٹھیک رکھیں\n4️⃣ گاہک سے رابطہ رکھیں\n5️⃣ اسٹیٹس فوری اپڈیٹ کریں\n6️⃣ شکایات کا جواب دیں";
            return $t($en, $ru, $us);
        }

        $pending = $ctx['pending_count'];
        $alert   = $pending > 0
            ? $t("⚠️ {$pending} pending request(s) need your attention!", "⚠️ {$pending} pending request(s) ka jawab dein!", "⚠️ {$pending} درخواستوں کا جواب دیں!")
            : $t("✅ No pending requests.", "✅ Koi pending request nahi.", "✅ کوئی زیر التوا درخواست نہیں۔");

        $en = "🤖 I'm **TruckLink Provider AI** — your business assistant!\n\n{$alert}\n\nI can help with:\n• 📋 Pending requests ({$pending})\n• 🚛 My vehicles ({$ctx['total_vehicles']})\n• ➕ Add/manage vehicles\n• ✅ Accept/Reject bookings\n• 📦 Update delivery status\n• 💰 Earnings & payments\n• ⭐ Ratings & reviews\n• 📋 Complaints\n• 💡 Tips for better earnings\n\nTry: *\"Pending requests\"* or *\"My earnings\"*";
        $ru = "🤖 Main **TruckLink Provider AI** hoon!\n\n{$alert}\n\nMain help kar sakta hoon:\n• 📋 Pending requests ({$pending})\n• 🚛 Meri vehicles ({$ctx['total_vehicles']})\n• ➕ Vehicle add/manage\n• ✅ Booking accept/reject\n• 📦 Delivery status update\n• 💰 Kamai\n• ⭐ Ratings\n• 📋 Complaints\n• 💡 Tips\n\nTry karein: *\"Pending requests\"* ya *\"Meri kamai\"*";
        $us = "🤖 میں **TruckLink Provider AI** ہوں!\n\n{$alert}\n\nمدد کر سکتا ہوں:\n• 📋 زیر التوا درخواستیں ({$pending})\n• 🚛 گاڑیاں ({$ctx['total_vehicles']})\n• 💰 کمائی\n• ⭐ ریٹنگ\n• 📋 شکایات\n• 💡 نکات\n\nلکھیں: *\"Pending requests\"* یا *\"Meri kamai\"*";
        return $t($en, $ru, $us);
    }

    // ══════════════════════════════════════════════════════════
    // FARE INFO
    // ══════════════════════════════════════════════════════════

    private function fareInfo(string $m, string $lang): string
    {
        $t = function(string $en, string $ru, string $us) use ($lang): string {
            if ($lang === self::LANG_URDU)       return $us;
            if ($lang === self::LANG_ROMAN_URDU) return $ru;
            return $en;
        };

        $distances = [
            'lahore-islamabad' => 375, 'lahore-karachi' => 1210,
            'islamabad-karachi' => 1410, 'lahore-peshawar' => 490,
            'lahore-multan' => 340, 'islamabad-peshawar' => 170,
            'karachi-hyderabad' => 165, 'lahore-faisalabad' => 130,
        ];
        $cities = ['lahore','islamabad','karachi','peshawar','multan','faisalabad','quetta','hyderabad','gujranwala','sialkot','rawalpindi'];
        $found  = [];
        foreach ($cities as $city) {
            if (str_contains($m, $city)) $found[] = $city;
        }

        if (count($found) >= 2) {
            $key  = $found[0] . '-' . $found[1];
            $dist = $distances[$key] ?? $distances[$found[1].'-'.$found[0]] ?? null;
            if ($dist) {
                $fare = 100 + ($dist * 25);
                $f0   = ucfirst($found[0]);
                $f1   = ucfirst($found[1]);
                return $t(
                    "💰 **{$f0} → {$f1} Fare:** ~Rs " . number_format($fare) . " (base Rs100 + Rs{$dist}×25/km) + tolls\n_Waiting: Rs 50/30min | Penalty: Rs 200/hr delay_",
                    "💰 **{$f0} → {$f1} Kiraya:** ~Rs " . number_format($fare) . " (base Rs100 + {$dist}km × Rs25) + tolls",
                    "💰 **{$f0} → {$f1} کرایہ:** ~Rs " . number_format($fare) . " (بیس Rs100 + {$dist}km × Rs25) + ٹول"
                );
            }
        }

        return $t(
            "💰 **Fare Formula:** Rs 100 base + Rs 25/km + Rs 50/30min wait + tolls\n**Late penalty: Rs 200/hour deducted from your fare.**\n\nShare cities for exact estimate!",
            "💰 **Kiraya Formula:** Rs 100 base + Rs 25/km + Rs 50/30min + toll\n**Deri penalty: Rs 200/ghanta aapke fare se katega.**",
            "💰 **کرایہ فارمولہ:** Rs 100 + Rs 25/km + Rs 50/30min + ٹول\n**تاخیر جرمانہ: Rs 200/گھنٹہ آپ کے فیئر سے کٹے گا۔**"
        );
    }

    // ══════════════════════════════════════════════════════════
    // SUGGESTIONS
    // ══════════════════════════════════════════════════════════

    private function getSuggestions(string $message, array $ctx, string $lang): array
    {
        $m = strtolower($message);

        $sets = [
            'en' => [
                'pending'   => ['How to accept booking?', 'My active trips', 'My earnings'],
                'vehicle'   => ['Add new vehicle', 'Vehicle status', 'Pending requests'],
                'booking'   => ['Update delivery status', 'Pending requests', 'My earnings'],
                'earning'   => ['Booking history', 'Penalty details', 'Tips to earn more'],
                'complaint' => ['My ratings', 'Active trips', 'Pending requests'],
                'delivery'  => ['Active trips', 'Update delivery status', 'My earnings'],
                'default'   => ['Pending requests', 'My vehicles', 'My earnings'],
            ],
            'ru' => [
                'pending'   => ['Booking accept kaise?', 'Active trips', 'Meri kamai'],
                'vehicle'   => ['Nayi vehicle add', 'Vehicle status', 'Pending requests'],
                'booking'   => ['Delivery status update', 'Pending requests', 'Kamai'],
                'earning'   => ['Booking history', 'Penalty details', 'Zyada kamai ke tips'],
                'complaint' => ['Meri rating', 'Active trips', 'Pending requests'],
                'delivery'  => ['Active trips', 'Delivery status update', 'Kamai'],
                'default'   => ['Pending requests', 'Meri vehicles', 'Meri kamai'],
            ],
            'us' => [
                'pending'   => ['بکنگ قبول کیسے؟', 'فعال سفر', 'میری کمائی'],
                'vehicle'   => ['نئی گاڑی شامل', 'گاڑی کی حالت', 'زیر التوا درخواستیں'],
                'booking'   => ['ڈیلیوری اسٹیٹس', 'زیر التوا درخواستیں', 'کمائی'],
                'earning'   => ['بکنگ تاریخ', 'جرمانہ', 'زیادہ کمائی'],
                'complaint' => ['میری ریٹنگ', 'فعال سفر', 'درخواستیں'],
                'delivery'  => ['فعال سفر', 'اسٹیٹس اپڈیٹ', 'کمائی'],
                'default'   => ['زیر التوا درخواستیں', 'میری گاڑیاں', 'میری کمائی'],
            ],
        ];

        $key = ($lang === self::LANG_URDU) ? 'us' : (($lang === self::LANG_ROMAN_URDU) ? 'ru' : 'en');
        $s   = $sets[$key];

        if (str_contains($m, 'pending') || str_contains($m, 'request')) return $s['pending'];
        if (str_contains($m, 'vehicle') || str_contains($m, 'truck') || str_contains($m, 'gaadi')) return $s['vehicle'];
        if (str_contains($m, 'booking')) return $s['booking'];
        if (str_contains($m, 'earning') || str_contains($m, 'kamai') || str_contains($m, 'paise') || str_contains($m, 'کمائی')) return $s['earning'];
        if (str_contains($m, 'complaint') || str_contains($m, 'shikayat') || str_contains($m, 'شکایت')) return $s['complaint'];
        if (str_contains($m, 'delivery') || str_contains($m, 'status') || str_contains($m, 'track')) return $s['delivery'];

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

    private function saveChat(int $providerId, string $sender, string $message): void
    {
        try {
            DB::table('chat_histories')->insert([
                'customer_id' => $providerId,
                'sender'      => $sender,
                'message'     => $message,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        } catch (\Exception $e) {
            Log::warning('Provider chat save failed: ' . $e->getMessage());
        }
    }

    // ══════════════════════════════════════════════════════════
    // SMARTCARD IMAGE EXTRACTION  — FIX #1: Proper key/model rotation
    // ══════════════════════════════════════════════════════════

 public function extractSmartCard(Request $request)
{
    // Validate BEFORE anything else so Laravel returns proper JSON
    try {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:8192',
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid file. Please upload a JPG or PNG image under 8MB.',
        ], 422);
    }
 
    $providerId = session('user_id');
    $role       = session('role');
 
    if (!$providerId || $role !== 'provider') {
        return response()->json([
            'success' => false,
            'message' => 'Session expired. Please log in again.',
        ], 401);
    }
 
    try {
        $imageFile = $request->file('image');
        $imageData = base64_encode(file_get_contents($imageFile->getRealPath()));
        $mimeType  = $imageFile->getMimeType();
 
        \Log::info("SmartCard: Processing image for provider {$providerId}, size: " . $imageFile->getSize() . " bytes, type: {$mimeType}");
 
    } catch (\Exception $e) {
        \Log::error("SmartCard: Failed to read image file: " . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Could not read the uploaded image. Please try again.',
        ], 500);
    }
 
    $extractionPrompt = "You are a vehicle document parser for Pakistan. Analyze this vehicle registration document / smart card / registration book image carefully.\n\nExtract ALL visible fields. Return ONLY valid JSON with no markdown, no explanation, no extra text:\n{\n  \"vehicle_number\": \"registration number like ABC-123 or ABC-1234 or LHR-1234\",\n  \"chassis_number\": \"chassis/VIN number (alphanumeric string)\",\n  \"engine_number\": \"engine number if visible\",\n  \"vehicle_type\": \"type like Truck, Mini Truck, Pickup, Van, Mazda, Loader, etc\",\n  \"make\": \"vehicle brand/manufacturer\",\n  \"model\": \"vehicle model name\",\n  \"year\": \"manufacturing or registration year (4 digits)\",\n  \"color\": \"vehicle color\",\n  \"owner_name\": \"registered owner full name\",\n  \"confidence\": \"high/medium/low\"\n}\n\nIf any field is not clearly visible, set it to null. Return ONLY the JSON object, nothing else.";
 
    // Try Gemini Vision first
    $result = $this->callGeminiVision($imageData, $mimeType, $extractionPrompt);
 
    // Fallback to Groq Vision if Gemini failed
    if (!$result['success']) {
        \Log::info('SmartCard: Gemini failed (' . ($result['error'] ?? 'unknown') . '), trying Groq Vision');
        $result = $this->callGroqVision($imageData, $mimeType, $extractionPrompt);
    }
 
    if (!$result['success']) {
        \Log::error('SmartCard: All providers failed for provider ' . $providerId);
        return response()->json([
            'success' => false,
            'message' => 'Could not extract data from image. Tips: ensure good lighting, document is flat, text is clearly visible, and image is not blurry.',
        ], 200); // 200 so frontend handles it as expected JSON
    }
 
    // Parse the AI response
    $jsonText = $result['text'];
    $jsonText = preg_replace('/```json\s*/i', '', $jsonText);
    $jsonText = preg_replace('/```\s*/i',     '', $jsonText);
 
    // Extract JSON object even if surrounded by extra text
    if (preg_match('/\{[\s\S]*?\}/s', $jsonText, $matches)) {
        $jsonText = $matches[0];
    }
 
    $jsonText = trim($jsonText);
    $parsed   = json_decode($jsonText, true);
 
    if (!$parsed) {
        \Log::warning('SmartCard: JSON parse failed. Provider: ' . ($result['provider'] ?? 'unknown') . '. Raw: ' . $jsonText);
        return response()->json([
            'success' => false,
            'message' => 'AI response was unclear. Please try a higher quality photo of the document.',
        ], 200);
    }
 
    // Remove null values for cleaner response
    $parsed = array_filter($parsed, fn($v) => $v !== null && $v !== '');
 
    \Log::info('SmartCard: ✅ Extraction success via ' . ($result['provider'] ?? 'unknown') . ' for provider ' . $providerId, $parsed);
 
    return response()->json([
        'success'  => true,
        'data'     => $parsed,
        'provider' => $result['provider'] ?? 'unknown',
        'message'  => 'Vehicle details extracted successfully!',
    ]);
}

    // ══════════════════════════════════════════════════════════
protected function callGroqVision(string $imageBase64, string $mimeType, string $prompt): array
{
    $groqKey = env('GROQ_API_KEY');
 
    if (!$groqKey) {
        \Log::error('GroqVision: No GROQ_API_KEY configured in .env');
        return ['success' => false, 'error' => 'Groq API key not configured.', 'provider' => 'groq'];
    }
 
    // ✅ Updated model list — decommissioned ones removed
    $visionModels = [
        'meta-llama/llama-4-scout-17b-16e-instruct',     // Best free vision (2025/2026)
        'meta-llama/llama-4-maverick-17b-128e-instruct', // Alternative
        'llama-3.2-90b-vision-preview',                  // Older fallback
    ];
 
    foreach ($visionModels as $model) {
        $slotKey = 'groq_vision_quota_' . md5($groqKey . $model);
 
        // Skip if this model was permanently marked as decommissioned
        $permanentBan = 'groq_vision_dead_' . md5($groqKey . $model);
        if (\Cache::has($permanentBan)) {
            \Log::info("GroqVision: Skipping {$model} (decommissioned/permanently unavailable)");
            continue;
        }
 
        // Skip if rate-limited recently
        if (\Cache::has($slotKey)) {
            \Log::info("GroqVision: Skipping {$model} (rate-limited in cache)");
            continue;
        }
 
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(45)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $groqKey,
                    'Content-Type'  => 'application/json',
                ])
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model'       => $model,
                    'max_tokens'  => 700,
                    'temperature' => 0.1,
                    'messages'    => [
                        [
                            'role'    => 'user',
                            'content' => [
                                [
                                    'type'      => 'image_url',
                                    'image_url' => [
                                        'url'    => "data:{$mimeType};base64,{$imageBase64}",
                                        'detail' => 'high', // High detail for document reading
                                    ],
                                ],
                                [
                                    'type' => 'text',
                                    'text' => $prompt,
                                ],
                            ],
                        ],
                    ],
                ]);
 
            $status = $response->status();
 
            if ($response->successful()) {
                $text = $response->json()['choices'][0]['message']['content'] ?? null;
 
                if ($text && strlen(trim($text)) > 5) {
                    \Log::info("GroqVision: ✅ Success with model:{$model}");
                    return ['success' => true, 'text' => trim($text), 'provider' => 'groq', 'model' => $model];
                }
 
                \Log::warning("GroqVision: {$model} returned empty response");
                continue;
            }
 
            $errBody = $response->json();
            $errMsg  = $errBody['error']['message'] ?? $response->body();
 
            // 429 = rate limit — cache temporarily
            if ($status === 429) {
                $retryIn = 60;
                if (preg_match('/retry[_\s]?after[:\s]+(\d+)/i', $errMsg, $m)) {
                    $retryIn = (int) $m[1];
                } elseif (preg_match('/(\d+(?:\.\d+)?)\s*s(?:ec|econds?)?/i', $errMsg, $m)) {
                    $retryIn = (int) ceil((float) $m[1]);
                }
                \Cache::put($slotKey, true, max($retryIn, 30));
                \Log::warning("GroqVision: {$model} rate-limited for {$retryIn}s");
                continue;
            }
 
            // 400 — check if decommissioned or image error
            if ($status === 400) {
                $isDecommissioned = str_contains($errMsg, 'decommissioned')
                    || str_contains($errMsg, 'no longer supported')
                    || str_contains($errMsg, 'deprecated');
 
                if ($isDecommissioned) {
                    // Permanently ban this model for 30 days
                    \Cache::put($permanentBan, true, now()->addDays(30));
                    \Log::warning("GroqVision: {$model} is decommissioned — permanently skipping");
                } else {
                    // Image-related 400 (e.g. too small, wrong format) — not model's fault
                    \Log::warning("GroqVision: {$model} 400 error (image issue?): " . substr($errMsg, 0, 200));
                }
                continue;
            }
 
            // 401/403 = auth error
            if ($status === 401 || $status === 403) {
                \Log::error("GroqVision: Auth error ({$status}) — check GROQ_API_KEY in .env");
                return [
                    'success'  => false,
                    'error'    => 'Groq authentication failed. Please verify GROQ_API_KEY in .env',
                    'provider' => 'groq',
                ];
            }
 
            \Log::warning("GroqVision: {$model} HTTP {$status}: " . substr($errMsg, 0, 200));
            continue;
 
        } catch (\Exception $e) {
            \Log::warning("GroqVision: Exception on {$model}: " . $e->getMessage());
            continue;
        }
    }
 
    return [
        'success'  => false,
        'error'    => 'Smart card extraction failed. Please try a clearer, well-lit photo of the document.',
        'provider' => 'groq',
    ];
}










protected function callGeminiVision(string $imageBase64, string $mimeType, string $prompt): array
{
    $apiKeys = $this->gemini->getApiKeys();
 
    if (empty($apiKeys)) {
        \Log::error('GeminiVision: No API keys available');
        return ['success' => false, 'error' => 'No Gemini API keys configured', 'provider' => 'gemini'];
    }
 
    // Vision-capable models only — best to fallback
    $visionModels = [
        'gemini-2.0-flash',
        'gemini-1.5-flash',
        'gemini-2.0-flash-lite',
        'gemini-1.5-flash-8b',
    ];
 
    foreach ($apiKeys as $keyIndex => $apiKey) {
        // Skip this key if it's globally quota-exhausted
        $keySlot = 'gemini_key_quota_' . md5($apiKey);
        if (\Cache::has($keySlot)) {
            \Log::info("GeminiVision: Skipping key#{$keyIndex} (quota exhausted in cache)");
            continue;
        }
 
        foreach ($visionModels as $model) {
            try {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";
 
                $body = [
                    'contents' => [
                        [
                            'role'  => 'user',
                            'parts' => [
                                [
                                    'inline_data' => [
                                        'mime_type' => $mimeType,
                                        'data'      => $imageBase64,
                                    ],
                                ],
                                ['text' => $prompt],
                            ],
                        ],
                    ],
                    'generationConfig' => [
                        'temperature'     => 0.1,
                        'maxOutputTokens' => 600,
                    ],
                    'safetySettings' => [
                        ['category' => 'HARM_CATEGORY_HARASSMENT',        'threshold' => 'BLOCK_NONE'],
                        ['category' => 'HARM_CATEGORY_HATE_SPEECH',       'threshold' => 'BLOCK_NONE'],
                        ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_NONE'],
                        ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_NONE'],
                    ],
                ];
 
                $response = \Illuminate\Support\Facades\Http::timeout(35)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post($url, $body);
 
                $status = $response->status();
 
                if ($response->successful()) {
                    $data = $response->json();
                    $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
 
                    if ($text && strlen(trim($text)) > 5) {
                        \Log::info("GeminiVision: success key#{$keyIndex} model:{$model}");
                        return ['success' => true, 'text' => trim($text), 'provider' => 'gemini'];
                    }
 
                    // Empty response — try next model
                    \Log::warning("GeminiVision: key#{$keyIndex} model:{$model} returned empty text");
                    continue;
                }
 
                // 404 = model not available, try next model
                if ($status === 404) {
                    \Log::warning("GeminiVision: model {$model} not found (404), skipping");
                    continue;
                }
 
                // 429 or 403 = quota/auth issue for this KEY — mark and break to next key
                if ($status === 429 || $status === 403) {
                    $errBody = $response->json();
                    $errMsg  = $errBody['error']['message'] ?? 'quota/auth error';
                    \Log::warning("GeminiVision: key#{$keyIndex} rate-limited ({$status}): {$errMsg}");
 
                    // Cache this key as exhausted until midnight PKT
                    $nowPKT   = now()->timezone('Asia/Karachi');
                    $midnight = $nowPKT->copy()->addDay()->startOfDay();
                    $seconds  = max($nowPKT->diffInSeconds($midnight), 60);
                    \Cache::put($keySlot, true, $seconds);
 
                    break; // Stop trying models for this key, move to next key
                }
 
                // Other HTTP errors — log and try next model
                $errBody = $response->json();
                $errMsg  = $errBody['error']['message'] ?? $response->body();
                \Log::warning("GeminiVision: key#{$keyIndex} model:{$model} error {$status}: " . substr($errMsg, 0, 200));
                continue;
 
            } catch (\Exception $e) {
                \Log::warning("GeminiVision: exception key#{$keyIndex} model:{$model}: " . $e->getMessage());
                continue;
            }
        }
    }
 
    return [
        'success'  => false,
        'error'    => 'All Gemini Vision attempts failed — trying Groq fallback.',
        'provider' => 'gemini',
    ];
}



}