<?php
// app/Http/Controllers/ReviewController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Booking;
use App\Models\Userr;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    /**
     * Check if user is logged in as customer
     */
    private function checkCustomerAuth()
    {
        if (!session()->has('user_id') || session('role') !== 'customer') {
            return false;
        }
        return true;
    }

    /**
     * Store a new review
     */
    public function store(Request $request)
    {
        // Check authentication
        if (!$this->checkCustomerAuth()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login as customer to submit review'
            ], 401);
        }

        // Validate request
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:bookings,id',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Get current customer
        $customerId = session('user_id');

        // Get booking
        $booking = Booking::with('vehicle.user')->find($request->booking_id);
        
        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found'
            ], 404);
        }

        // Check if booking belongs to this customer
        if ($booking->customer_id != $customerId) {
            return response()->json([
                'success' => false,
                'message' => 'You can only review your own bookings'
            ], 403);
        }

        // Check if booking is completed
        if ($booking->status !== 'complete') {
            return response()->json([
                'success' => false,
                'message' => 'You can only review completed bookings'
            ], 400);
        }

        // Check if review already exists
        $existingReview = Review::where('booking_id', $booking->id)
            ->where('customer_id', $customerId)
            ->first();
        
        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'You have already reviewed this booking'
            ], 400);
        }

        // Get provider ID from vehicle
        $providerId = $booking->vehicle->user_id ?? null;

        // Create review
        $review = Review::create([
            'booking_id' => $booking->id,
            'customer_id' => $customerId,
            'provider_id' => $providerId,
            'rating' => $request->rating,
            'review' => $request->review,
            'is_approved' => true
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Review submitted successfully',
            'data' => [
                'review' => $review,
                'rating_stars' => $review->rating_stars
            ]
        ]);
    }

    /**
     * Get reviews for a booking
     */
    public function getBookingReviews($bookingId)
    {
        // Check authentication
        if (!$this->checkCustomerAuth()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to view reviews'
            ], 401);
        }

        $reviews = Review::where('booking_id', $bookingId)
            ->with(['customer' => function($q) {
                $q->select('id', 'name');
            }])
            ->approved()
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $reviews
        ]);
    }

    /**
     * Update a review
     */
    public function update(Request $request, $reviewId)
    {
        // Check authentication
        if (!$this->checkCustomerAuth()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to update review'
            ], 401);
        }

        $customerId = session('user_id');
        
        $review = Review::where('id', $reviewId)
            ->where('customer_id', $customerId)
            ->first();

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found or you don\'t have permission to edit'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'rating' => 'sometimes|integer|min:1|max:5',
            'review' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->has('rating')) {
            $review->rating = $request->rating;
        }
        if ($request->has('review')) {
            $review->review = $request->review;
        }
        
        $review->save();

        return response()->json([
            'success' => true,
            'message' => 'Review updated successfully',
            'data' => $review
        ]);
    }

    /**
     * Delete a review
     */
    public function destroy($reviewId)
    {
        // Check authentication
        if (!$this->checkCustomerAuth()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to delete review'
            ], 401);
        }

        $customerId = session('user_id');
        
        $review = Review::where('id', $reviewId)
            ->where('customer_id', $customerId)
            ->first();

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found or you don\'t have permission to delete'
            ], 404);
        }

        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Review deleted successfully'
        ]);
    }














// admin see reviews


    public function ratingsReviews()
{
    return view('admin.ratings-reviews');
}

 public function aiReviews()
{
    return view('admin.aiReviews');
}
/**
 * Get ratings and reviews data for DataTable
 */
public function getRatingsReviewsData(Request $request)
{
    $query = Review::with(['booking', 'customer', 'provider', 'booking.vehicle']);
    
    // Filter by rating
    if ($request->has('rating') && $request->rating != 'all') {
        $query->where('rating', $request->rating);
    }
    
    // Filter by status
    if ($request->has('status') && $request->status != 'all') {
        $isApproved = $request->status === 'approved';
        $query->where('is_approved', $isApproved);
    }
    
    // Search
    if ($request->has('search') && !empty($request->search)) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('review', 'like', "%{$search}%")
              ->orWhereHas('customer', function($cq) use ($search) {
                  $cq->where('name', 'like', "%{$search}%")
                     ->orWhere('email', 'like', "%{$search}%");
              })
              ->orWhereHas('provider', function($pq) use ($search) {
                  $pq->where('name', 'like', "%{$search}%")
                     ->orWhere('email', 'like', "%{$search}%");
              })
              ->orWhereHas('booking', function($bq) use ($search) {
                  $bq->where('id', 'like', "%{$search}%");
              });
        });
    }
    
    $reviews = $query->orderBy('created_at', 'desc')->paginate(15);
    
    // Calculate statistics
    $stats = [
        'total' => Review::count(),
        'avg_rating' => round(Review::avg('rating') ?? 0, 1),
        'five_star' => Review::where('rating', 5)->count(),
        'four_star' => Review::where('rating', 4)->count(),
        'three_star' => Review::where('rating', 3)->count(),
        'two_star' => Review::where('rating', 2)->count(),
        'one_star' => Review::where('rating', 1)->count(),
        'approved' => Review::where('is_approved', true)->count(),
        'pending_approval' => Review::where('is_approved', false)->count(),
    ];
    
    return response()->json([
        'success' => true,
        'reviews' => $reviews->items(),
        'current_page' => $reviews->currentPage(),
        'last_page' => $reviews->lastPage(),
        'total' => $reviews->total(),
        'per_page' => $reviews->perPage(),
        'stats' => $stats
    ]);
}

/**
 * Toggle review approval status
 */
public function toggleReviewStatus(Request $request, $id)
{
    $review = Review::findOrFail($id);
    $review->is_approved = !$review->is_approved;
    $review->save();
    
    return response()->json([
        'success' => true,
        'message' => $review->is_approved ? 'Review approved successfully' : 'Review hidden from public',
        'is_approved' => $review->is_approved
    ]);
}

/**
 * Delete a review
 */
public function deleteReview($id)
{
    $review = Review::findOrFail($id);
    $review->delete();
    
    return response()->json([
        'success' => true,
        'message' => 'Review deleted successfully'
    ]);
}




// provider see his reviews
// provider see his reviews
public function Provider_ratingsReviews()
{
    $providerId = session('user_id');

    if (!$providerId || session('role') != 'provider') {
        return redirect()->route('login')->with('error', 'Access denied.');
    }

    // Get provider name
    $provider = Userr::find($providerId);
    $providerName = $provider ? $provider->name : 'Provider';

    // Get provider's vehicles
    $vehicles = Vehicle::where('user_id', $providerId)->get();
    
    // Get all reviews for provider
    $allReviews = Review::where('provider_id', $providerId)
        ->with(['customer', 'booking', 'booking.vehicle'])
        ->orderBy('created_at', 'desc')
        ->get();
    
    // Group reviews by vehicle
    $reviewsByVehicle = [];
    $totalRatingSum = 0;
    $totalReviewCount = 0;
    
    foreach ($vehicles as $vehicle) {
        $reviewsByVehicle[$vehicle->id] = $allReviews->filter(function($review) use ($vehicle) {
            return $review->booking && $review->booking->vehicle_id == $vehicle->id;
        });
        
        $vehicleReviews = $reviewsByVehicle[$vehicle->id];
        $totalRatingSum += $vehicleReviews->sum('rating');
        $totalReviewCount += $vehicleReviews->count();
    }
    
    // Calculate average rating (avoid division by zero)
    $averageRating = $totalReviewCount > 0 ? round($totalRatingSum / $totalReviewCount, 1) : 0;
    
    // Define total reviews count
    $totalReviews = $totalReviewCount;
    
    // Pass all data to view
    return view('provider.ratings_reviews', [
        'vehicles' => $vehicles,
        'reviewsByVehicle' => $reviewsByVehicle,
        'totalReviews' => $totalReviews,
        'averageRating' => $averageRating,
        'providerName' => $providerName,
        
    ]);
}





// customer see reviews in with vehicle in findvehicle.blade.php


/**
 * Get reviews for a specific vehicle
 */
 public function getVehicleReviews($id)
    {
        try {
            // Load vehicle with user relationship
            $vehicle = Vehicle::with('user')->findOrFail($id);
            
            // Get reviews through completed bookings
            $reviews = Review::whereHas('booking', function($query) use ($id) {
                $query->where('vehicle_id', $id)
                      ->where('status', 'complete');
            })
            ->with('customer')
            ->where('is_approved', true)
            ->orderBy('created_at', 'desc')
            ->get();
            
            // Calculate average rating
            $averageRating = $reviews->avg('rating') ?? 0;
            $totalReviews = $reviews->count();
            
            // Calculate rating distribution
            $distribution = [
                1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0
            ];
            
            foreach ($reviews as $review) {
                if (isset($distribution[$review->rating])) {
                    $distribution[$review->rating]++;
                }
            }
            
            // Format reviews for JSON response
            $formattedReviews = $reviews->map(function ($review) {
                return [
                    'id' => $review->id,
                    'rating' => $review->rating,
                    'review' => $review->review,
                    'customer_name' => $review->customer ? $review->customer->name : 'Anonymous',
                    'created_at' => $review->created_at->format('M d, Y'),
                ];
            });
            
            // Also return vehicle details for the modal
            $vehicleData = [
                'id' => $vehicle->id,
                'vehicle_type' => $vehicle->vehicle_type,
                'vehicle_number' => $vehicle->vehicle_number,
                'weight_capacity' => $vehicle->weight_capacity,
                'can_carry' => $vehicle->can_carry,
                'chassis_number' => $vehicle->chassis_number,
                'vehicle_image' => $vehicle->vehicle_image ? asset('uploads/vehicles/' . $vehicle->vehicle_image) : null,
                'owner_name' => $vehicle->user ? $vehicle->user->name : 'N/A',
                'owner_email' => $vehicle->user ? $vehicle->user->email : 'N/A',
                'status' => 'Available'
            ];
            
            return response()->json([
                'success' => true,
                'vehicle' => $vehicleData,
                'average_rating' => round($averageRating, 1),
                'total_reviews' => $totalReviews,
                'rating_distribution' => $distribution,
                'reviews' => $formattedReviews
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error fetching vehicle reviews: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch reviews: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Helper function to generate stars HTML
     */
    private function getStarsHtml($rating)
    {
        $fullStars = floor($rating);
        $halfStar = ($rating - $fullStars) >= 0.5;
        $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
        
        $html = '';
        for ($i = 0; $i < $fullStars; $i++) {
            $html .= '<i class="fas fa-star text-warning"></i>';
        }
        if ($halfStar) {
            $html .= '<i class="fas fa-star-half-alt text-warning"></i>';
        }
        for ($i = 0; $i < $emptyStars; $i++) {
            $html .= '<i class="far fa-star text-warning"></i>';
        }
        
        return $html;
    }




































































































}