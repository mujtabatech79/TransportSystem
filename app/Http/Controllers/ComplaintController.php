<?php
// app/Http/Controllers/ComplaintController.php

namespace App\Http\Controllers;
use App\Models\Userr;
use Illuminate\Http\Request;
use App\Models\Complaint;
use App\Models\Booking;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Validator;

class ComplaintController extends Controller
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
     * Store a new complaint
     */
    public function store(Request $request)
    {
        // Check authentication
        if (!$this->checkCustomerAuth()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login as customer to register complaint'
            ], 401);
        }

        // Validate request
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:bookings,id',
            'complaint_type' => 'required|in:late_delivery,damaged_goods,rude_driver,other',
            'subject' => 'required|string|max:255',
            'description' => 'required|string|max:1000'
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
                'message' => 'You can only complain about your own bookings'
            ], 403);
        }

        // Get provider ID from vehicle
        $providerId = $booking->vehicle->user_id ?? null;

        // Create complaint
        $complaint = Complaint::create([
            'booking_id' => $booking->id,
            'customer_id' => $customerId,
            'provider_id' => $providerId,
            'complaint_type' => $request->complaint_type,
            'subject' => $request->subject,
            'description' => $request->description,
            'status' => 'pending'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Complaint registered successfully',
            'data' => $complaint
        ]);
    }

    /**
     * Get complaints for a booking
     */
    public function getBookingComplaints($bookingId)
    {
        // Check authentication
        if (!$this->checkCustomerAuth()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to view complaints'
            ], 401);
        }

        $customerId = session('user_id');
        
        $complaints = Complaint::where('booking_id', $bookingId)
            ->where('customer_id', $customerId)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $complaints
        ]);
    }

    /**
     * Get all complaints for current customer
     */
    public function getMyComplaints()
    {
        // Check authentication
        if (!$this->checkCustomerAuth()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to view complaints'
            ], 401);
        }

        $customerId = session('user_id');
        
        $complaints = Complaint::where('customer_id', $customerId)
            ->with(['booking' => function($q) {
                $q->select('id', 'pickup_location', 'dropoff_location', 'status');
            }])
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $complaints
        ]);
    }

    /**
     * Get complaint details
     */
    public function show($complaintId)
    {
        // Check authentication
        if (!$this->checkCustomerAuth()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to view complaint'
            ], 401);
        }

        $customerId = session('user_id');
        
        $complaint = Complaint::where('id', $complaintId)
            ->where('customer_id', $customerId)
            ->with(['booking', 'provider' => function($q) {
                $q->select('id', 'name', 'email');
            }])
            ->first();

        if (!$complaint) {
            return response()->json([
                'success' => false,
                'message' => 'Complaint not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $complaint
        ]);
    }

    /**
     * Cancel a complaint (only if pending)
     */
    public function cancel($complaintId)
    {
        // Check authentication
        if (!$this->checkCustomerAuth()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to cancel complaint'
            ], 401);
        }

        $customerId = session('user_id');
        
        $complaint = Complaint::where('id', $complaintId)
            ->where('customer_id', $customerId)
            ->first();

        if (!$complaint) {
            return response()->json([
                'success' => false,
                'message' => 'Complaint not found'
            ], 404);
        }

        if ($complaint->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending complaints can be cancelled'
            ], 400);
        }

        $complaint->status = 'cancelled';
        $complaint->save();

        return response()->json([
            'success' => true,
            'message' => 'Complaint cancelled successfully'
        ]);
    }


















































    // customer se his complaints


/**
 * Show customer complaints page
 */
// In your Customer Controller (e.g., app/Http/Controllers/CustomerController.php)

public function myComplaints()
{
    // Session se user ID lein
    $customerId = session('user_id');
    
    if (!$customerId) {
        return redirect()->route('login')->with('error', 'Please login first');
    }
    
    // Fetch all complaints for this customer with relationships
    $complaints = Complaint::where('customer_id', $customerId)
        ->with(['booking', 'booking.vehicle', 'booking.vehicle.user', 'booking.customer'])
        ->orderBy('created_at', 'desc')
        ->get();
    
    // Get statistics
    $totalComplaints = $complaints->count();
    $pendingComplaints = $complaints->where('status', 'pending')->count();
    $resolvedComplaints = $complaints->where('status', 'resolved')->count();
    $inReviewComplaints = $complaints->where('status', 'in_review')->count();
    
    $userName = session('name', 'Customer');
    
    return view('customer.seecomplaints', [
        'complaints' => $complaints,
        'totalComplaints' => $totalComplaints,
        'pendingComplaints' => $pendingComplaints,
        'resolvedComplaints' => $resolvedComplaints,
        'inReviewComplaints' => $inReviewComplaints,
        'userName' => $userName
    ]);
}












// provider se his complaints
/**
 * Display complaints for vehicle owner
 */
public function complaints()
{
    $providerId = session('user_id');

    if (!$providerId || session('role') != 'provider') {
        return redirect()->route('login')->with('error', 'Access denied.');
    }

    // Get all vehicles owned by this provider
    $vehicleIds = Vehicle::where('user_id', $providerId)->pluck('id');
    
    // Get all complaints related to bookings of these vehicles
    $complaints = Complaint::with(['booking.customer', 'booking.vehicle', 'customer', 'provider'])
        ->whereHas('booking', function($query) use ($vehicleIds) {
            $query->whereIn('vehicle_id', $vehicleIds);
        })
        ->orderBy('created_at', 'desc')
        ->get();
    
    // Get provider name for UI
    $provider = Userr::find($providerId);
    $userName = $provider ? $provider->name : 'Vehicle Owner';
    
    // Get statistics
    $totalComplaints = $complaints->count();
    $pendingComplaints = $complaints->where('status', 'pending')->count();
    $inReviewComplaints = $complaints->where('status', 'in_review')->count();
    $resolvedComplaints = $complaints->where('status', 'resolved')->count();
    
    // Count by type
    $lateDeliveryCount = $complaints->filter(function($c) {
        $type = strtolower($c->complaint_type ?? '');
        return $type === 'late_delivery' || $type === 'late delivery';
    })->count();
    
    $damagedGoodsCount = $complaints->filter(function($c) {
        $type = strtolower($c->complaint_type ?? '');
        return $type === 'damaged_goods' || $type === 'damaged goods';
    })->count();
    
    $rudeDriversCount = $complaints->filter(function($c) {
        $type = strtolower($c->complaint_type ?? '');
        return $type === 'rude_driver' || $type === 'rude driver' || $type === 'rude drivers';
    })->count();
    
    $otherCount = $complaints->filter(function($c) {
        $type = strtolower($c->complaint_type ?? '');
        return !in_array($type, ['late_delivery', 'late delivery', 'damaged_goods', 'damaged goods', 'rude_driver', 'rude driver', 'rude drivers']);
    })->count();
    
    return view('Provider.provider_complaints', compact(
        'complaints', 
        'userName',
        'totalComplaints',
        'pendingComplaints',
        'inReviewComplaints',
        'resolvedComplaints',
        'lateDeliveryCount',
        'damagedGoodsCount',
        'rudeDriversCount',
        'otherCount'
    ));
}
}