<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GeminiService;
use App\Models\Review;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AIReviewController extends Controller
{
    protected $gemini;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    // ══════════════════════════════════════════════════════════
    // MAIN PAGE
    // ══════════════════════════════════════════════════════════

    public function index()
    {
        return view('admin.aiReviews');
    }

    // ══════════════════════════════════════════════════════════
    // GET CATEGORIZED REVIEWS (AI-powered)
    // ══════════════════════════════════════════════════════════

    public function getCategorizedReviews(Request $request)
    {
        $category      = $request->get('category', 'all');   // all | positive | negative | neutral
        $page          = (int) $request->get('page', 1);
        $perPage       = (int) $request->get('per_page', 10);
        $ratingFilter  = $request->get('rating', 'all');
        $statusFilter  = $request->get('status', 'all');
        $sortBy        = $request->get('sort', 'newest');
        $search        = $request->get('search', '');

        // ── Fetch all reviews with relations ──────────────────
        $query = Review::with(['booking', 'customer', 'provider', 'booking.vehicle']);

        if ($ratingFilter !== 'all') {
            $query->where('rating', (int) $ratingFilter);
        }

        if ($statusFilter !== 'all') {
            $query->where('is_approved', $statusFilter === 'approved');
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('review', 'like', "%{$search}%")
                  ->orWhereHas('customer', fn($cq) =>
                      $cq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                  )
                  ->orWhereHas('provider', fn($pq) =>
                      $pq->where('name', 'like', "%{$search}%")
                  );
            });
        }

        switch ($sortBy) {
            case 'oldest':   $query->orderBy('created_at', 'asc'); break;
            case 'highest':  $query->orderBy('rating', 'desc');    break;
            case 'lowest':   $query->orderBy('rating', 'asc');     break;
            default:         $query->orderBy('created_at', 'desc'); break;
        }

        $allReviews = $query->get();

        // ── Categorize each review ────────────────────────────
        $categorized = $this->categorizeReviews($allReviews);

        // ── Filter by category ────────────────────────────────
        if ($category !== 'all') {
            $filteredReviews = collect($categorized['reviews'])->where('ai_category', $category)->values();
        } else {
            $filteredReviews = collect($categorized['reviews'])->values();
        }

        // ── Paginate manually ─────────────────────────────────
        $total    = $filteredReviews->count();
        $lastPage = max(1, (int) ceil($total / $perPage));
        $page     = min($page, $lastPage);
        $offset   = ($page - 1) * $perPage;
        $items    = $filteredReviews->slice($offset, $perPage)->values();

        // ── Stats ─────────────────────────────────────────────
        $allCategorized = collect($categorized['reviews']);
        $stats = [
            'total'           => Review::count(),
            'avg_rating'      => round(Review::avg('rating') ?? 0, 1),
            'five_star'       => Review::where('rating', 5)->count(),
            'four_star'       => Review::where('rating', 4)->count(),
            'three_star'      => Review::where('rating', 3)->count(),
            'two_star'        => Review::where('rating', 2)->count(),
            'one_star'        => Review::where('rating', 1)->count(),
            'approved'        => Review::where('is_approved', true)->count(),
            'pending_approval'=> Review::where('is_approved', false)->count(),
            'positive_count'  => $allCategorized->where('ai_category', 'positive')->count(),
            'negative_count'  => $allCategorized->where('ai_category', 'negative')->count(),
            'neutral_count'   => $allCategorized->where('ai_category', 'neutral')->count(),
        ];

        return response()->json([
            'success'      => true,
            'reviews'      => $items,
            'current_page' => $page,
            'last_page'    => $lastPage,
            'total'        => $total,
            'per_page'     => $perPage,
            'stats'        => $stats,
            'ai_used'      => $categorized['ai_used'],
        ]);
    }

    // ══════════════════════════════════════════════════════════
    // CATEGORIZE REVIEWS — AI + Rule Fallback
    // ══════════════════════════════════════════════════════════

    private function categorizeReviews($reviews): array
    {
        $reviewsArray = $reviews->toArray();

        if (empty($reviewsArray)) {
            return ['reviews' => [], 'ai_used' => false];
        }

        // Build a batch prompt for AI classification
        $aiUsed = false;
        $cacheKey = 'ai_review_categories_' . md5(json_encode(array_column($reviewsArray, 'id')));

        $cachedCategories = Cache::get($cacheKey);

        if (!$cachedCategories) {
            $cachedCategories = $this->classifyWithAI($reviewsArray);
            if ($cachedCategories) {
                Cache::put($cacheKey, $cachedCategories, now()->addMinutes(30));
                $aiUsed = true;
            }
        } else {
            $aiUsed = true;
        }

        // Merge AI categories or use rule-based fallback
        foreach ($reviewsArray as &$review) {
            if ($cachedCategories && isset($cachedCategories[$review['id']])) {
                $review['ai_category'] = $cachedCategories[$review['id']];
                $aiUsed = true;
            } else {
                // Rule-based fallback
                $review['ai_category'] = $this->ruleBasedCategory($review);
            }
        }

        return ['reviews' => $reviewsArray, 'ai_used' => $aiUsed];
    }

    // ── AI Classification (batch) ─────────────────────────────

    private function classifyWithAI(array $reviews): ?array
    {
        // Build a compact batch prompt
        $lines = [];
        foreach ($reviews as $r) {
            $text   = strip_tags($r['review'] ?? '');
            $rating = $r['rating'] ?? 0;
            if (empty(trim($text))) {
                $text = "(no text, rating: {$rating})";
            }
            $lines[] = "ID:{$r['id']}|RATING:{$rating}|REVIEW:" . mb_substr($text, 0, 120);
        }

        $batchText = implode("\n", $lines);

        $systemPrompt = "You are a sentiment classifier for logistics service reviews. Classify each review as EXACTLY one of: positive, negative, or neutral.\n\n" .
            "Rules:\n" .
            "- positive: good/excellent service, happy customer, 4-5 stars with praise\n" .
            "- negative: complaints, bad experience, poor service, low rating (1-2)\n" .
            "- neutral: average/ok, mixed feelings, factual with no strong sentiment, 3 stars with no clear lean\n\n" .
            "Return ONLY a JSON object like: {\"ID\": \"category\", \"ID\": \"category\", ...}\n" .
            "No markdown, no extra text, just raw JSON.";

        $userPrompt = "Classify these reviews:\n\n{$batchText}\n\nReturn only JSON mapping ID to category (positive/negative/neutral).";

        try {
            $response = $this->gemini->generate($userPrompt, $systemPrompt);

            if (!$response) return null;

            // Strip markdown if present
            $json = preg_replace('/```json\s*/i', '', $response);
            $json = preg_replace('/```\s*/i', '', $json);
            if (preg_match('/\{[\s\S]*\}/s', $json, $m)) {
                $json = $m[0];
            }

            $parsed = json_decode(trim($json), true);

            if (!is_array($parsed)) return null;

            // Normalize keys and values
            $normalized = [];
            foreach ($parsed as $id => $cat) {
                $id  = (int) $id;
                $cat = strtolower(trim($cat));
                if (!in_array($cat, ['positive', 'negative', 'neutral'])) {
                    $cat = 'neutral';
                }
                $normalized[$id] = $cat;
            }

            Log::info('AIReviewController: AI classified ' . count($normalized) . ' reviews');
            return $normalized;

        } catch (\Exception $e) {
            Log::warning('AIReviewController: AI classification failed: ' . $e->getMessage());
            return null;
        }
    }

    // ── Rule-based fallback ───────────────────────────────────

    private function ruleBasedCategory(array $review): string
    {
        $rating = (int) ($review['rating'] ?? 3);
        $text   = mb_strtolower($review['review'] ?? '');

        // Positive keywords
        $positiveWords = ['excellent', 'great', 'amazing', 'wonderful', 'fantastic', 'perfect',
            'love', 'best', 'awesome', 'outstanding', 'superb', 'brilliant', 'happy',
            'satisfied', 'good', 'nice', 'fast', 'reliable', 'professional', 'recommend',
            'acha', 'bahut acha', 'shukriya', 'zabardast', 'maza', 'behtareen'];

        // Negative keywords
        $negativeWords = ['bad', 'terrible', 'awful', 'horrible', 'worst', 'poor', 'slow',
            'late', 'rude', 'unprofessional', 'disappointed', 'never', 'waste',
            'complaint', 'problem', 'issue', 'broke', 'damaged', 'missing',
            'bura', 'ganda', 'deri', 'masla', 'takleef', 'naqabil'];

        $positiveScore = 0;
        $negativeScore = 0;

        foreach ($positiveWords as $word) {
            if (str_contains($text, $word)) $positiveScore++;
        }
        foreach ($negativeWords as $word) {
            if (str_contains($text, $word)) $negativeScore++;
        }

        // Rating-weighted decision
        if ($rating >= 4 || ($rating === 3 && $positiveScore > $negativeScore)) {
            return $positiveScore > 0 || $rating >= 4 ? 'positive' : 'neutral';
        }
        if ($rating <= 2 || ($rating === 3 && $negativeScore > $positiveScore)) {
            return $negativeScore > 0 || $rating <= 2 ? 'negative' : 'neutral';
        }

        return 'neutral';
    }

    // ══════════════════════════════════════════════════════════
    // RE-ANALYZE (force refresh cache)
    // ══════════════════════════════════════════════════════════

    public function reAnalyze(Request $request)
    {
        // Clear all cached AI categories
        $allIds = Review::pluck('id')->toArray();
        $cacheKey = 'ai_review_categories_' . md5(json_encode($allIds));
        Cache::forget($cacheKey);

        // Also clear any subset caches by flushing all related
        // (In production use tagged cache; here we just clear the main one)
        Log::info('AIReviewController: Cache cleared for re-analysis');

        return response()->json(['success' => true, 'message' => 'Re-analysis triggered. Refresh the page.']);
    }

    // ══════════════════════════════════════════════════════════
    // TOGGLE STATUS  (keep existing functionality)
    // ══════════════════════════════════════════════════════════

    public function toggleReviewStatus(Request $request, $id)
    {
        $review = Review::findOrFail($id);
        $review->is_approved = !$review->is_approved;
        $review->save();

        return response()->json([
            'success'     => true,
            'message'     => $review->is_approved ? 'Review approved successfully' : 'Review hidden from public',
            'is_approved' => $review->is_approved,
        ]);
    }

    // ══════════════════════════════════════════════════════════
    // DELETE REVIEW  (keep existing functionality)
    // ══════════════════════════════════════════════════════════

    public function deleteReview($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Review deleted successfully',
        ]);
    }
}